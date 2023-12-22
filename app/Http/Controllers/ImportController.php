<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patron;
use App\Models\Collection;
use App\Models\Import;
use \PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ImportController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('imports.index');
        }

        $imports = Import::with(['librarian:id,first_name,last_name'])->select()->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'imports.archive') {
            $imports = $imports->onlyTrashed();
        }

        return Datatables::of($imports)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action"> 
                    <a href="' . url('imports') . '/' . $row->id . '">' . $this->button('fa fa-eye', $row->id, 'view') . '</a>';

                if (Route::currentRouteName() == 'imports.index') {
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'delete');
                } else if (Route::currentRouteName() == 'imports.archive') {
                    $html .= $this->button('fa fa-undo', $row->id, 'restore');
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'force-delete');
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->addColumn('librarian', function ($row) {
                return '<a href="' . route("patrons.index") . '/' . $row->librarian_id . '">' . $row->librarian->last_name . ', ' . $row->librarian->first_name . '</a>';            })
            ->filterColumn('librarian', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('librarian', function ($subquery) use ($keyword) {
                        $subquery->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                });
            })
            ->rawColumns(['action', 'checkbox', 'librarian'])
            ->make(true);
    }

    public function show($id)
    {
        $import = Import::withTrashed()->with('librarian:id,first_name,last_name')->findOrFail($id);

        return view('imports.show')
            ->with([
                'import' => $import,
                'importStatus' => $import->deleted_at == null ? 'active' : 'archived'
            ]);
    }

    public function importPatrons(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'patron_file' => ['required'],
        ]);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        // GET CURRENT SPREADSHEET'S ARRAY
        $reader = IOFactory::createReader('Xlsx')->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('patron_file'))->getActiveSheet()->toArray();

        // GET HEADERS AND RENAME IT TO THE ORIGINAL FIELD NAME
        $header = $spreadsheet[0];
        $header = array_map('strtolower', $header);

        $fieldNames = [
            'id' => 'id2',
            'first name' => 'first_name',
            'last name' => 'last_name',
            'group' => 'groups',
            'role' => 'roles'
        ];

        $fields = $this->mapColumns($header, $fieldNames);
        $fillables = app(Patron::class)->getFillable();

        $failInsertionErrors = [];
        $successCount = 0;

        $import = Import::create([
            'librarian_id' => auth()->user()->id,
            'table' => 'Patrons'
        ]);

        // LOOP EACH ROW
        for ($row = 1; $row < count($spreadsheet); $row++) {
            $patron = new Patron();

            $dataToValidate = [];

            // LOOP THROUGH FIELDS TO COMBINE INTO ARRAY AND VALIDATE LATER
            foreach ($fields as $field) {
                if (in_array($field, $fillables) || $field == 'groups') {
                    $column = array_search($field, $fields);
                    $dataToValidate[$field] = $spreadsheet[$row][$column];
                }
            }

            if (!array_key_exists('roles', $dataToValidate)) {
                $dataToValidate['roles'] = $request->roles;
            }

            // VALIDATION
            $validator = $this->patronRules($dataToValidate);

            // LOOP THROUGH VALIDATION MESSAGES AND STORE TO IMPORT FAILURES
            if ($validator->fails()) {
                $failInsertionErrors[] = [$dataToValidate, $validator->errors()];
                $importFailureValues = [];
                $importFailureErrors = [];

                foreach ($dataToValidate as $key => $value) {
                    $importFailureValues[$key] = $value;

                    if($validator->errors()->get($key)){
                        $importFailureErrors[$key] = $validator->errors()->get($key);
                    }
                }

                $import->importFailures()->create([
                    'values' => $importFailureValues,
                    'errors' => $importFailureErrors,
                ]);

                $import->update([
                    $import->success_count = $successCount,
                    $import->failed_count = count($failInsertionErrors),
                    $import->total_records = $successCount + count($failInsertionErrors)
                ]);                
                
                continue;
            }

            // STORE THE INFORMATION TO PATRON INSTANCE
            foreach ($fields as $field) {
                if (in_array($field, $fillables)) {
                    $column = array_search($field, $fields);
                    $patron->$field = $spreadsheet[$row][$column];
                }
            }

            $patron->librarian_id = auth()->user()->id;
            $patron->save();

            // ADD GROUPS TO PATRONS
            $column = array_search('groups', $fields);

            if ($column > -1) {
                $groups = explode(';', $spreadsheet[$row][$column]);

                foreach ($groups as $group) {
                    $model = \App\Models\Group::firstOrCreate(['group' => Str::title($group)]);
                    $patron->groups()->attach($model);
                }
            }

            // ADD ROLES TO PATRONS
            $column = array_search('roles', $fields);
            $roles = null;

            if ($column > -1) {
                $roles = array_map(function ($role) {
                    return strtolower($role);
                }, explode(';', $spreadsheet[$row][$column]));

                $patron->syncRoles($roles);
            } else {
                $patron->syncRoles($request->roles);
            }

            $successCount++;
            
            $import->update([
                $import->success_count = $successCount,
                $import->failed_count = count($failInsertionErrors),
                $import->total_records = $successCount + count($failInsertionErrors)
            ]);
        }
        if ($successCount > 0) {
            return response()->json(['success' => $successCount . ' patron(s) successfully inserted! ' . count($failInsertionErrors) . ' failed.']);
        } else {
            return response()->json(['error' => 'All patrons failed to insert!']);
        }
    }

    public function importCollections(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'collection_file' => ['required'],
        ]);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        // GET CURRENT SPREADSHEET'S ARRAY
        $reader = IOFactory::createReader('Xlsx')->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('collection_file'))->getActiveSheet()->toArray();

        // GET HEADERS AND RENAME IT TO THE ORIGINAL FIELD NAME
        $header = $spreadsheet[0];
        $header = array_map('strtolower', $header);

        $fieldNames = [
            'author' => 'authors',
            'subtitle' => 'subtitles',
            'subject' => 'subjects',
            'call' => 'call_number',
            'call number' => 'call_number',
            'series title' => 'series_title',
            'publication place' => 'publication_place',
            'copyright year' => 'copyright_year',
            'copyright' => 'copyright_year',
            'physical description' => 'physical_description',
            'description' => 'physical_description',
        ];

        $fields = $this->mapColumns($header, $fieldNames);
        $fillables = app(Collection::class)->getFillable();

        $failInsertionErrors = [];
        $successCount = 0;

        $import = Import::create([
            'librarian_id' => auth()->user()->id,
            'table' => 'Collections'
        ]);

        // LOOP EACH ROW
        for ($row = 1; $row < count($spreadsheet); $row++) {
            $collection = new Collection();

            $dataToValidate = [];

            // LOOP THROUGH FIELDS TO COMBINE INTO ARRAY AND VALIDATE LATER
            foreach ($fields as $field) {
                if (in_array($field, $fillables) || $field == 'authors' || $field == 'subjects' || $field == 'subtitles') {
                    $column = array_search($field, $fields);
                    $dataToValidate[$field] = $spreadsheet[$row][$column];
                }

                if ($field == 'call_number') {
                    $column = array_search($field, $fields);
                    $callNumber = explode(' ', $spreadsheet[$row][$column]);
                    $hasPrefix = \App\Models\Setting::where('field', 'prefix')->first()
                        ->holdingOptions()
                        ->where('value', $callNumber[0])
                        ->exists();

                    $dataToValidate['call_prefix'] = $hasPrefix ? ($callNumber[0] ?? null) : null;
                    $dataToValidate['call_main'] = $hasPrefix ? ($callNumber[1] ?? null) : ($callNumber[0] ?? null);
                    $dataToValidate['call_cutter'] = $hasPrefix ? ($callNumber[2] ?? null) : ($callNumber[1] ?? null);
                    $dataToValidate['call_suffix'] = $hasPrefix ? ($callNumber[3] ?? null) : ($callNumber[2] ?? null);
                }
            }

            // IF FORMAT COLUMN NOT INDICATED ON EXCEL
            if (!array_key_exists('format', $dataToValidate)) {
                $dataToValidate['format'] = $request->format;
            }

            // VALIDATION
            $validator = $this->collectionRules($dataToValidate);

            // LOOP THROUGH VALIDATION MESSAGES AND STORE TO IMPORT FAILURES
            if ($validator->fails()) {
                $failInsertionErrors[] = [$dataToValidate, $validator->errors()];
                $importFailureValues = [];
                $importFailureErrors = [];

                foreach ($dataToValidate as $key => $value) {
                    $importFailureValues[$key] = $value;

                    if($validator->errors()->get($key)){
                        $importFailureErrors[$key] = $validator->errors()->get($key);
                    }
                }

                $import->importFailures()->create([
                    'values' => $importFailureValues,
                    'errors' => $importFailureErrors,
                ]);

                $import->update([
                    $import->success_count = $successCount,
                    $import->failed_count = count($failInsertionErrors),
                    $import->total_records = $successCount + count($failInsertionErrors)
                ]);                
                
                continue;
            }

            // STORE THE INFORMATION TO COLLECTION INSTANCE
            foreach ($dataToValidate as $key => $value) {
                if (in_array($key, $fillables)) {
                    $column = array_search($key, $fields);
                    $collection->$key = $value;
                }
            }

            $collection->librarian_id = auth()->user()->id;
            $collection->save();

            // ADD AUTHORS TO COLLECTION
            $column = array_search('authors', $fields);

            if ($column > -1) {
                $authors = explode(';', $spreadsheet[$row][$column]);

                foreach ($authors as $author) {
                    $author = \App\Models\Author::firstOrCreate(['author' => Str::title($author)]);
                    $collection->authors()->attach($author);
                }
            }

            // ADD SUBJECTS TO COLLECTION
            $column = array_search('subjects', $fields);

            if ($column > -1) {
                $subjects = explode(';', $spreadsheet[$row][$column]);

                foreach ($subjects as $subject) {
                    $subject = \App\Models\Subject::firstOrCreate(['subject' => Str::title($subject)]);
                    $collection->subjects()->attach($subject);
                }
            }

            // ADD SUBTITLES TO COLLECTION
            $column = array_search('subtitles', $fields);

            if ($column > -1) {
                $subtitles = explode(';', $spreadsheet[$row][$column]);

                foreach ($subtitles as $subtitle) {
                    $model = new \App\Models\Subtitle(['subtitle' => Str::title($subtitle)]);
                    $collection->subtitles()->save($model);
                }
            }


            $successCount++;

            $import->update([
                $import->success_count = $successCount,
                $import->failed_count = count($failInsertionErrors),
                $import->total_records = $successCount + count($failInsertionErrors)
            ]);
        }
        if ($successCount > 0) {
            return response()->json(['success' => $successCount . ' collection(s) successfully inserted! ' . count($failInsertionErrors) . ' failed.']);
        } else {
            return response()->json(['error' => 'All collections failed to insert!']);
        }
    }

    public function importCopies(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'copy_file' => ['required'],
        ]);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        // GET CURRENT SPREADSHEET'S ARRAY
        $reader = IOFactory::createReader('Xlsx')->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file('copy_file'))->getActiveSheet()->toArray();

        // GET HEADERS AND RENAME IT TO THE ORIGINAL FIELD NAME
        $header = $spreadsheet[0];
        $header = array_map('strtolower', $header);

        $fieldNames = [
            'call' => 'call_number',
            'call number' => 'call_number',
            'author' => 'authors',
            'date acquired' => 'date_acquired',
            'date' => 'date_acquired'
        ];

        $fields = $this->mapColumns($header, $fieldNames);
        $copyFillables = app(\App\Models\Copy::class)->getFillable();
        $collectionFillables = app(\App\Models\Collection::class)->getFillable();

        $failInsertionErrors = [];
        $successCount = 0;

        $import = Import::create([
            'librarian_id' => auth()->user()->id,
            'table' => 'Copies'
        ]);

        // LOOP EACH ROW
        for ($row = 1; $row < count($spreadsheet); $row++) {
            $collection = null;
            $callPrefix = null;
            $callMain = null;
            $callCutter = null;
            $callSuffix = null;
            $copy = new \App\Models\Copy();

            $dataToValidate = [];
            $callNumbers = [];

            // FIND COPY'S COLLECTION
            foreach ($fields as $field) {
                if (in_array($field, $copyFillables) | in_array($field, $collectionFillables) | $field == 'authors') {
                    $column = array_search($field, $fields);
                    $dataToValidate[$field] = $spreadsheet[$row][$column];
                }

                if ($field == 'call_number') {
                    $column = array_search($field, $fields);
                    $callNumber = explode(' ', $spreadsheet[$row][$column]);
                    $hasPrefix = \App\Models\Setting::where('field', 'prefix')->first()
                        ->holdingOptions()
                        ->where('value', $callNumber[0])
                        ->exists();

                    $callPrefix = $hasPrefix ? ($callNumber[0] ?? null) : null;
                    $callMain = $hasPrefix ? ($callNumber[1] ?? null) : ($callNumber[0] ?? null);
                    $callCutter = $hasPrefix ? ($callNumber[2] ?? null) : ($callNumber[1] ?? null);
                    $callSuffix = $hasPrefix ? ($callNumber[3] ?? null) : ($callNumber[2] ?? null);

                    $callNumbers[] = [$callPrefix, $callMain, $callCutter, $callSuffix];

                    $collection = \App\Models\Collection::where('call_main', $callMain)
                        ->where('call_cutter', $callCutter)
                        ->where('call_suffix', $callSuffix)
                        ->first();
                }
            }

            // VALIDATE COLLECTION FIELDS IF THE CALL NUMBER DOES NOT EXIST AND NEEDED TO CREATE COLLECTION
            if (!$collection) {
                $collectionValidator = $this->collectionRules($dataToValidate);

                if ($collectionValidator->fails()) {
                        $failInsertionErrors[] = [$dataToValidate, $collectionValidator->errors()];
                        $importFailureValues = [];
                        $importFailureErrors = [];
        
                        foreach ($dataToValidate as $key => $value) {
                            $importFailureValues[$key] = $value;
        
                            if($collectionValidator->errors()->get($key)){
                                $importFailureErrors[$key] = $collectionValidator->errors()->get($key);
                            }
                        }
        
                        $import->importFailures()->create([
                            'values' => $importFailureValues,
                            'errors' => $importFailureErrors,
                        ]);
        
                        $import->update([
                            $import->success_count = $successCount,
                            $import->failed_count = count($failInsertionErrors),
                            $import->total_records = $successCount + count($failInsertionErrors)
                        ]);                
                        
                        continue;
                }
            }

            // IF FUND / VENDOR / LOCATION COLUMN NOT INDICATED ON EXCEL
            if (!array_key_exists('fund', $dataToValidate)) {
                $dataToValidate['fund'] = $request->fund;
            }

            if (!array_key_exists('vendor', $dataToValidate)) {
                $dataToValidate['vendor'] = $request->vendor;
            }

            // VALIDATE COPY FIELDS
            $copyValidator = $this->copyRules($dataToValidate);

            // LOOP THROUGH VALIDATION MESSAGES AND STORE TO IMPORT FAILURES
            if ($copyValidator->fails()) {
                $failInsertionErrors[] = [$dataToValidate, $copyValidator->errors()];
                $importFailureValues = [];
                $importFailureErrors = [];

                foreach ($dataToValidate as $key => $value) {
                    $importFailureValues[$key] = $value;

                    if($copyValidator->errors()->get($key)){
                        $importFailureErrors[$key] = $copyValidator->errors()->get($key);
                    }
                }

                $import->importFailures()->create([
                    'values' => $importFailureValues,
                    'errors' => $importFailureErrors,
                ]);

                $import->update([
                    $import->success_count = $successCount,
                    $import->failed_count = count($failInsertionErrors),
                    $import->total_records = $successCount + count($failInsertionErrors)
                ]);                
                
                continue;
            }

            // IF COLLECTION DOES NOT EXIST
            if (!$collection) {
                $collection = \App\Models\Collection::create([
                    'title' => $dataToValidate['title'],
                    'call_main' => $callMain,
                    'call_cutter' => $callCutter,
                    'call_suffix' => $callSuffix
                ]);

                // ADD AUTHORS
            }

            $collection->librarian_id = auth()->user()->id;
            $copy->collection_id = $collection->id;

            // STORE THE COPY INFO TO COLLECTION
            foreach ($dataToValidate as $key => $value) {
                if (in_array($key, $copyFillables)) {
                    $column = array_search($key, $fields);
                    $copy->$key = $value;
                }
            }

            $copy->call_prefix = $callPrefix;
            $copy->save();
            $successCount++;

            $import->update([
                $import->success_count = $successCount,
                $import->failed_count = count($failInsertionErrors),
                $import->total_records = $successCount + count($failInsertionErrors)
            ]);
        }

        if ($successCount > 0) {
            return response()->json(['success' => $successCount . ($successCount > 1 ? 'copies' : 'copy') . ' successfully inserted! ' . count($failInsertionErrors) . ' failed.']);
        } else {
            return response()->json(['error' => 'All copies failed to insert!']);
        }
    }

    private function patronRules($dataToValidate)
    {
        $rules = [
            'id2' => ['required', 'numeric', Rule::unique('patrons')],
            'first_name' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'min:2', 'max:30'],
            'last_name' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'min:2', 'max:20'],
            'email' => ['required', 'email', Rule::unique('patrons')],
            'groups' => ['nullable', new \App\Rules\MultivaluedMax(5)],
            'roles' => ['required', new \App\Rules\MultivaluedMax(2)]
        ];

        $validator = Validator::make($dataToValidate, $rules);
        $validator->setAttributeNames(['id2' => 'id']); // Replace 'id2' with 'id' in the attribute names

        return $validator;
    }

    private function collectionRules($dataToValidate)
    {
        $rules = [
            'format' => ['nullable'],
            'title' => ['required', 'string'],
            'series_title' => ['nullable', 'string'],
            'isbn' => [new \App\Rules\IsbnLength],
            'publication_place' => ['nullable'],
            'publisher' => ['nullable'],
            'copyright_year' => ['nullable', 'integer', 'date_format:Y', 'before_or_equal:' . date('Y')],
            'physical_description' => ['nullable', 'string'],
            'image' => ['nullable', 'sometimes', 'mimes:jpeg,png,webp,jpg', 'max:1000'],
            'authors' => ['nullable', new \App\Rules\MultivaluedMax(5)],
            'subjects' => ['nullable', new \App\Rules\MultivaluedMax(5)],
            'subtitles' => ['nullable', new \App\Rules\MultivaluedMax(5)],
        ];

        $validator = Validator::make($dataToValidate, $rules);

        return $validator;
    }

    private function copyRules($dataToValidate)
    {
        $rules = [
            'barcode' => ['required', 'string', 'max:10', Rule::unique('copies')],
            'price' => ['nullable', 'numeric', 'min:0'],
            'fund' => ['nullable', 'in:donated,purchased'],
            'date_acquired' => ['nullable', 'date'],
        ];

        $validator = Validator::make($dataToValidate, $rules);

        return $validator;
    }

    private function button($icon, $id, $className)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-import-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '"> <i class="' . $icon . '"></i> </button>';
    }

    private function mapColumns($columns, $fieldNames)
    {
        $newColumns = [];
        foreach ($columns as $column) {
            if (array_key_exists($column, $fieldNames)) {
                $newColumns[] = $fieldNames[$column];
            } else {
                $newColumns[] = $column;
            }
        }

        return $newColumns;
    }

    public function destroy(Request $request)
    {
        $message = 'Import';

        if (is_array($request->id)) {
            Import::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            Import::findOrFail($request->id)->delete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function restore(Request $request)
    {
        $message = 'Import';

        if (is_array($request->id)) {
            Import::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            Import::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Import';

        if (is_array($request->id)) {
            Import::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            Import::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }
}
