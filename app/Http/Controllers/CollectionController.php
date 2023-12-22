<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Collection;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CollectionController extends Controller
{
    private function rules(Request $request, Collection $collection = null)
    {
        $rules = [
            'format' => ['required'],
            'title' => ['required'],
            'edition' => ['nullable'],
            'series_title' => ['nullable'],
            'isbn' => [new \App\Rules\IsbnLength],
            'publication_place' => ['nullable'],
            'publisher' => ['nullable'],
            'copyright_year' => ['nullable', 'date_format:Y', 'before_or_equal:' . date('Y')],
            'physical_description' => ['nullable', 'max:255'],
            'image' => ['nullable', 'sometimes', 'mimes:jpeg,png,webp,jpg', 'max:1000'],
            'authors' => ['nullable', new \App\Rules\MultivaluedMax(5)],
            'subjects' => ['nullable', new \App\Rules\MultivaluedMax(5)],
            'subtitles' => ['nullable', new \App\Rules\MultivaluedMax(5)],
            'call_main' => ['nullable'],
            'call_cutter' => ['nullable'],
            'call_suffix' => ['nullable'],
        ];

        $validator = Validator::make($request->all(), $rules);

        return $validator;
    }

    private function button($icon, $id, $className, $disabled = false)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-collection-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '" ' . ($disabled == true ? 'disabled style="background: none !important;"' : '') . '><i class="' . $icon . '"></i></button>';
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            $formats = \App\Models\Setting::where('field', 'format')->first()->holdingOptions()->get()->pluck('value')->toArray();

            return view('collections.index')->with([
                'formats' => $formats
            ]);
        }

        $collections = Collection::with(['authors', 'images'])
            ->select('id', 'title', 'format', 'total_copies')
            ->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'collections.archive') {
            $collections = $collections->onlyTrashed();
        }

        return Datatables::of($collections)
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                $html .= '<a href="' . route('collections.show', $row->id) . '">' . $this->button('fa fa-eye', $row->id, 'view') . '</a>';

                if (auth()->check()) {
                    if (auth()->user()->temp_role == 'librarian' & Route::currentRouteName() == 'collections.index') {
                        $html .= $this->button('fa fa-edit', $row->id, 'edit');

                        if(!$row->copies()
                        ->where(function ($query) {
                            $query->where('availability', 'reserved')
                                ->orWhere('availability', 'on loan');
                        })
                        ->exists()){
                            $html .= $this->button('fa-solid fa-trash-can', $row->id, 'delete');
                        }
                    }

                    if (Route::currentRouteName() == 'collections.archive') {
                        $html .= $this->button('fa fa-undo', $row->id, 'restore');
                        $html .= $this->button('fa-solid fa-trash-can', $row->id, 'force-delete');
                    }
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('image', function ($row) {
                $image = $row->images()->latest()->first();
                $file_name = $image->file_name ?? 'default.jpg';

                return '<div class="avatar avatar-xl"><img src="' . asset('storage/images/collections/' . $file_name) . '" class="avatar-img rounded"></div>';
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->addColumn('title', function ($row) {
                return Str::limit($row->title, 30, '...');
            })
            ->addColumn('authors', function ($row) {
                $html = '';

                foreach ($row->authors as $author) {
                    $html .= '<span class="badge badge-count"><strong>' . $author->author . '</strong></span>';
                }

                return $html;
            })
            ->filterColumn('authors', function ($query, $keyword) {
                $query->whereHas('authors', function ($q) use ($keyword) {
                    $q->where('author', 'like', '%' . $keyword . '%');
                });
            })
            ->addColumn('availability', function ($row) {
                $availableCopies = $row->copies()->where('availability', 'available')->count();
                return $availableCopies > 0 ? '<span class="badge badge-count">' . $availableCopies . ' item(s) left</span>' : '<span class="badge badge-warning">unavailable</span>';
            })
            ->filterColumn('availability', function ($query, $keyword) {
                if ($keyword == 'available') {
                    $query->whereHas('copies', function ($subquery) {
                        $subquery->where('availability', 'available');
                    });
                } elseif ($keyword == 'unavailable') {
                    $query->whereDoesntHave('copies', function ($subquery) {
                        $subquery->where('availability', 'available');
                    });
                }
            })
            ->addColumn('disabled', function ($row) {
                return $row->copies()
                    ->where(function ($query) {
                        $query->where('availability', 'reserved')
                            ->orWhere('availability', 'on loan');
                    })
                    ->exists();
            })
            ->rawColumns(['action', 'image', 'checkbox', 'authors', 'availability'])
            ->make(true);
    }

    function convertToLastNameFirst($fullName)
    {
        $names = explode(' ', $fullName);

        if (strpos($fullName, ',') !== false) {
            return $fullName;
        }

        $lastName = array_pop($names);

        return $lastName . ', ' . implode(' ', $names);
    }


    public function store(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $collection = Collection::create([
            'librarian_id' => auth()->user()->id,
            'format' => $request->format,
            'title' => Str::title($request->title),
            'edition' => $request->edition,
            'series_title' => Str::title($request->series_title),
            'isbn' => $request->isbn,
            'publication_place' => Str::title($request->publication_place),
            'publisher' => Str::title($request->publisher),
            'copyright_year' => $request->copyright_year,
            'physical_description' => $request->physical_description,
            'call_main' => $request->call_main,
            'call_cutter' => $request->call_cutter,
            'call_suffix' => $request->call_suffix
        ]);

        // ADD IMAGE
        if ($request->hasFile('image')) {
            $directoryPath = 'images/collections';

            if (!Storage::exists($directoryPath)) {
                Storage::makeDirectory($directoryPath);
            }

            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            Storage::disk('public')->putFileAs($directoryPath, $request->file('image'), $fileName);

            $collection->images()->create([
                'file_name' => $fileName
            ]);
        }

        // ADD AUTHORS TO A BOOK
        if (!empty($request->authors)) {
            $authors = json_decode($request->authors, true); //

            foreach ($authors as $author) {
                $model = \App\Models\Author::firstOrCreate(['author' => $this->convertToLastNameFirst(Str::title($author))]);
                $collection->authors()->attach($model);
            }
        }

        // ADD SUBJECTS TO A BOOK
        if (!empty($request->subjects)) {
            $subjects = json_decode($request->subjects, true); //

            foreach ($subjects as $subject) {
                $model = \App\Models\Subject::firstOrCreate(['subject' => Str::title($subject)]);
                $collection->subjects()->attach($model);
            }
        }

        // ADD SUBTITLES TO A BOOK
        if (!empty($request->subtitles)) {
            $subtitles = json_decode($request->subtitles, true); //

            foreach ($subtitles as $subtitle) {
                $model = new \App\Models\Subtitle(['subtitle' => Str::title($subtitle)]);
                $collection->subtitles()->save($model);
            }
        }

        return response()->json(['success' => 'Collection added successfully.']);
    }

    public function edit($id)
    {
        $collection = Collection::findOrFail($id);

        $authors = $collection->authors()->pluck('author')->toArray();
        $subjects = $collection->subjects()->pluck('subject')->toArray();
        $subtitles = $collection->subtitles()->pluck('subtitle')->toArray();
        $image = $collection->images()->first();
        if ($image != null) {
            $image = asset('storage/images/collections/' . $image->file_name);
        }

        return response()->json([
            'collection' => $collection,
            'authors' => $authors,
            'subjects' => $subjects,
            'subtitles' => $subtitles,
            'image' => $image
        ]);
    }

    public function show($id)
    {
        $collection = Collection::withTrashed()->with('authors:author', 'subtitles:subtitle,collection_id', 'subjects:subject', 'images')->findOrFail($id);
        $formats = \App\Models\Setting::where('field', 'format')->first()->holdingOptions()->get()->pluck('value')->toArray();
        $prefixes = \App\Models\Setting::where('field', 'prefix')->first()->holdingOptions()->pluck('value')->toArray();

        return view('collections.show')->with([
            'collection' => $collection,
            'collectionStatus' => $collection->deleted_at == null ? 'active' : 'archived',
            'formats' => $formats,
            'prefixes' => $prefixes
        ]);
    }

    public function update(Request $request, $id)
    {
        $collection = Collection::where('id', $id)->first();

        $validated = $this->rules($request, $collection);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $collection->librarian_id = auth()->user()->id;
        $collection->format = Str::title($request->format);
        $collection->title = Str::title($request->title);
        $collection->edition = Str::title($request->edition);
        $collection->series_title = Str::title($request->series_title);
        $collection->isbn = $request->isbn;
        $collection->publication_place = Str::title($request->publication_place);
        $collection->publisher = Str::title($request->publisher);
        $collection->copyright_year = $request->copyright_year;
        $collection->physical_description = $request->physical_description;
        $collection->call_main = $request->call_main;
        $collection->call_cutter = $request->call_cutter;
        $collection->call_suffix = $request->call_suffix;

        $collection->save();

        //  ADD IMAGE
        $collection->images()->delete();

        if ($request->hasFile('image')) {
            $directoryPath = 'images/collections';

            if (!Storage::exists($directoryPath)) {
                Storage::makeDirectory($directoryPath);
            }

            $extension = $request->file('image')->getClientOriginalExtension();
            $fileName = date('YmdHis') . '_' . uniqid() . '.' . $extension;
            Storage::disk('public')->putFileAs($directoryPath, $request->file('image'), $fileName);

            $collection->images()->create([
                'file_name' => $fileName
            ]);
        }

        // ADD AUTHORS TO A BOOK
        if (!empty($request->authors)) {
            $authors = json_decode($request->authors, true); //

            $authorIds = [];
            foreach ($authors as $author) {
                $authorModel = \App\Models\Author::firstOrCreate(['author' => Str::title($author)]);
                $authorIds[] = $authorModel->id;
            }

            $collection->authors()->sync($authorIds);
        }

        // ADD SUBJECTS TO A BOOK
        if (!empty($request->subjects)) {
            $subjects = json_decode($request->subjects, true); //

            $subjectIds = [];
            foreach ($subjects as $subject) {
                $subjectModel = \App\Models\Subject::firstOrCreate(['subject' => Str::title($subject)]);
                $subjectIds[] = $subjectModel->id;
            }

            $collection->subjects()->sync($subjectIds);
        }

        // ADD SUBTITLES TO A BOOK
        if (!empty($request->subtitles)) {
            $subtitles = json_decode($request->subtitles, true); //

            $collection->subtitles()->delete();

            foreach ($subtitles as $subtitle) {
                $collection->subtitles()->create(['subtitle' => $subtitle]);
            }
        }

        return response()->json(['success' => 'Collection updated successfully.']);
    }

    public function destroy(Request $request)
    {
        $message = 'Collection';

        if (is_array($request->id)) {
            Collection::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            Collection::findOrFail($request->id)->delete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function restore(Request $request)
    {
        $message = 'Collection';

        if (is_array($request->id)) {
            Collection::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            Collection::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';

            if (!$request->ajax()) {
                return redirect()->route('collections.index')->withInput()->with('message', 'Collection has been successfully restored!');
            }
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Collection';

        if (is_array($request->id)) {
            Collection::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            Collection::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';

            if (!$request->ajax()) {
                return redirect()->route('collections.index')->withInput()->with('message', 'Collection has been successfully force deleted!');
            }
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }
}
