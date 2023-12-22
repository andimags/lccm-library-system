<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Jimmyjs\ReportGenerator\Facades\PdfReportFacade;
use Jimmyjs\ReportGenerator\Facades\ExcelReportFacade;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;

class ReportController extends Controller
{
    private function rules(Request $request)
    {
        $rules = [
            'fields' => ['required', 'min:1'],
            'sort_by' => ['required'],
            'sort_order' => ['required'],
            'file_type' => ['required', 'in:pdf,excel'],
            'created_at_start' => ['nullable', 'required_with:created_at_end', 'date'],
            'created_at_end' => ['nullable', 'required_with:created_at_start', 'date', 'after_or_equal:created_at_start'],
        ];

        $validator = Validator::make($request->all(), $rules);

        return $validator;
    }

    private function button($icon, $id, $className)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-report-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '"> <i class="' . $icon . '"></i> </button>';
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            $formats = \App\Models\Setting::where('field', 'format')->first()->holdingOptions()->get()->pluck('value')->toArray();

            return view('reports.index')->with(['formats' => $formats]);
        }

        $reports = \App\Models\Report::with(['librarian:id,first_name,last_name'])
            ->select()
            ->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'reports.archive') {
            $reports = $reports->onlyTrashed();
        }

        return Datatables::of($reports)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                if (Route::currentRouteName() == 'reports.index') {
                    $html .= $this->button($row->file_type == 'Pdf' ? 'fa fa-eye' : 'fa-solid fa-download', $row->id, 'download');
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'delete');
                } else if (Route::currentRouteName() == 'reports.archive') {
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
                return '<a href="' . route("patrons.index") . '/' . $row->librarian_id . '">' . $row->librarian->last_name . ', ' . $row->librarian->first_name . '</a>';
            })
            ->filterColumn('librarian', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('librarian', function ($subquery) use ($keyword) {
                        $subquery->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                });
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d h:i A');
            })
            ->rawColumns(['action', 'checkbox', 'librarian'])
            ->make(true);
    }

    public function patronsList(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $fields = $request->fields;
        $sortBy = $request->sort_by;
        $sortOrder = $request->sort_order;

        $columnMappings = [
            'id2' => ['ID', null],
            'first_name' => ['First name', null],
            'last_name' => ['Last name', null],
            'email' => ['Email', null],
            'roles' => ['Roles', function ($patron) {
                return $patron->getRoleNames()->implode(', ');
            }],
            'groups' => ['Groups', function ($patron) {
                return implode(', ', $patron->groups->pluck('group')->toArray());
            }],
            'created_at' => ['Created at', function ($patron) {
                return $patron->created_at->format('Y-m-d h:i A');
            }],
            'total_unpaid_fines' => ['Total unpaid fines', function ($patron) {
                return number_format($patron->totalUnpaidFines(), 2, '.', ',');
            }],
        ];

        $columns = [];

        foreach ($fields as $field) {
            if (isset($columnMappings[$field])) {
                [$displayName, $callback] = $columnMappings[$field];
                $columns[$displayName] = $callback ?? $field;
            }
        }


        $title = $request->report_type; // Report title

        $meta = [
            'Datetime' => Carbon::now(),
            'Sort by' => $sortBy == 'id2' ? 'id' : $sortBy,
            'Sort order' => $sortOrder,
            'Librarian' => auth()->user()->last_name . ', ' . auth()->user()->first_name
        ];

        $patrons = \App\Models\Patron::select()
            ->with('groups:id,group')
            ->orderBy($sortBy, $sortOrder);

        if ($request->roles != 'all') {
            $patrons = $patrons->role($request->roles);
        }

        if ($request->created_at_start && $request->created_at_end) {
            $start = Carbon::parse($request->created_at_start);
            $end = Carbon::parse($request->created_at_end);

            if ($start == $end) {
                $patrons = $patrons->whereDate('created_at', $start);
            } else {
                $patrons = $patrons->whereBetween('created_at', [$start->subDays(1), $end->addDays(1)]);
            }
        }

        if ($request->report_type == 'Patron Registrations List') {
            $patrons = $patrons->where('registration_status', 'pending');
        }

        if ($request->report_type == 'Delinquent Patrons List') {
            $patrons = $patrons->where(function ($query) {
                $query->whereHas('offSiteCirculations', function ($subQuery) {
                    $subQuery->where('fines_status', 'unpaid');
                });
            });
        }

        $directoryPath = 'public/reports';
        $fileName = str_replace(' ', '_', $request->report_type) . '_' . now()->format('Y-m-d_H-i-s');

        Report::create([
            'report_type' => $request->report_type,
            'fields' => implode(', ', $fields),
            'sort_by' => $request->sort_by,
            'sort_order' => $request->sort_order,
            'file_type' => Str::title($request->file_type),
            'file_name' => $fileName . ($request->file_type == 'pdf' ? '.pdf' : '.xlsx'),
            'librarian_id' => auth()->user()->id
        ]);

        if (!Storage::disk('public')->exists($directoryPath)) {
            Storage::disk('public')->makeDirectory($directoryPath);
        }

        if ($request->file_type == 'pdf') {
            $pdf = PdfReportFacade::of($title, $meta, $patrons, $columns)
                ->setOrientation('landscape');

            if (!empty($request->limit)) {
                $pdf->limit($request->limit);
            }

            $pdf->store("{$directoryPath}/{$fileName}.pdf");
        } elseif ($request->file_type == 'excel') {
            $excel = ExcelReportFacade::of($title, $meta, $patrons, $columns)
                ->simple();

            if (!empty($request->limit)) {
                $excel->limit($request->limit);
            }

            $excel->store("{$directoryPath}/{$fileName}.xlsx");
        }
    }

    public function collectionsList(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $fields = $request->fields;
        $sortBy = $request->sort_by;
        $sortOrder = $request->sort_order;

        $columnMappings = [
            'format' => ['Format', null],
            'title' => ['Title', null],
            'edition' => ['Edition', null],
            'series_title' => ['Series Title', null],
            'isbn' => ['ISBN', null],
            'publication_place' => ['Publication Place', null],
            'publisher' => ['Publisher', null],
            'copyright_year' => ['Copyright Year', null],
            'physical_description' => ['Physical Description', null],
            'authors' => ['Authors', function ($collection) {
                return implode(', ', $collection->authors->pluck('author')->toArray());
            }],
            'subtitles' => ['Subtitles', function ($collection) {
                return implode(', ', $collection->subtitles->pluck('subtitle')->toArray());
            }],
            'subjects' => ['Subjects', function ($collection) {
                return implode(', ', $collection->subjects->pluck('subject')->toArray());
            }],
            'created_at' => ['Created at', function ($collection) {
                return $collection->created_at->format('Y-m-d');
            }],
            'call_number' => ['Call Number', function ($collection) {
                $callNumber = ($collection->call_main ? $collection->call_main . ' ' : '') .
                    ($collection->call_cutter ? $collection->call_cutter . ' ' : '') .
                    ($collection->call_suffix ? $collection->call_suffix : '');

                return $callNumber;
            }],
        ];

        $columns = [];

        foreach ($fields as $field) {
            if (isset($columnMappings[$field])) {
                [$displayName, $callback] = $columnMappings[$field];
                $columns[$displayName] = $callback ?? $field;
            }
        }


        $title = $request->report_type; // Report title

        $meta = [
            'Datetime' => Carbon::now(),
            'Sort by' => $sortBy,
            'Sort order' => $sortOrder,
            'Librarian' => auth()->user()->last_name . ', ' . auth()->user()->first_name
        ];

        $collections = \App\Models\Collection::select()
            ->with('authors:id,author', 'subtitles:id,subtitle', 'subjects:id,subject')
            ->orderBy($sortBy, $sortOrder);

        if ($request->format != 'all') {
            $collections = $collections->where('format', $request->format);
        }

        if ($request->created_at_start && $request->created_at_end) {
            $start = Carbon::parse($request->created_at_start);
            $end = Carbon::parse($request->created_at_end);

            if ($start == $end) {
                $collections = $collections->whereDate('created_at', $start);
            } else {
                $collections = $collections->whereBetween('created_at', [$start->subDays(1), $end->addDays(1)]);
            }
        }

        $directoryPath = 'public/reports';
        $fileName = 'Collections_List_' . now()->format('Y-m-d_H-i-s');

        Report::create([
            'report_type' => $request->report_type,
            'fields' => implode(', ', $fields),
            'sort_by' => $request->sort_by,
            'sort_order' => $request->sort_order,
            'file_type' => Str::title($request->file_type),
            'file_name' => $fileName . ($request->file_type == 'pdf' ? '.pdf' : '.xlsx'),
            'librarian_id' => auth()->user()->id
        ]);

        if (!Storage::disk('public')->exists($directoryPath)) {
            Storage::disk('public')->makeDirectory($directoryPath);
        }

        if ($request->file_type == 'pdf') {
            $pdf = PdfReportFacade::of($title, $meta, $collections, $columns)
                ->setOrientation('landscape');

            if (!empty($request->limit)) {
                $pdf->limit($request->limit);
            }

            $pdf->store("{$directoryPath}/{$fileName}.pdf");
        } elseif ($request->file_type == 'excel') {
            $excel = ExcelReportFacade::of($title, $meta, $collections, $columns)
                ->simple();

            if (!empty($request->limit)) {
                $excel->limit($request->limit);
            }

            $excel->store("{$directoryPath}/{$fileName}.xlsx");
        }
    }

    public function copiesList(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $columnMappings = [
            'title' => ['Title', function ($copy) {
                return $copy->collection->title;
            }],
            'authors' => ['Authors', function ($copy) {
                return implode(', ', $copy->collection->authors->pluck('author')->toArray());
            }],
            'barcode' => ['Barcode', null],
            'price' => ['Price', null],
            'fund' => ['Fund', null],
            'vendor' => ['Vendor', null],
            'copyright_year' => ['Copyright Year', null],
            'availability' => ['Availability', null],
            'call_number' => ['Call Number', function ($copy) {
                $callNumber = ($copy->call_prefix ? $copy->call_prefix . ' ' : '') .
                    ($copy->collection->call_main ? $copy->collection->call_main . ' ' : '') .
                    ($copy->collection->call_cutter ? $copy->collection->call_cutter . ' ' : '') .
                    ($copy->collection->call_suffix ? $copy->collection->call_suffix : '');

                return $callNumber;
            }],
            'acquired_at' => ['Acquired At', function ($copy) {
                return $copy->acquired_at ? $copy->acquired_at->format('Y-m-d') : null;
            }],
            'created_at' => ['Created At', function ($copy) {
                return $copy->created_at->format('Y-m-d h:i A');
            }]
        ];

        $columns = [];

        foreach ($request->fields as $field) {
            if (isset($columnMappings[$field])) {
                [$displayName, $callback] = $columnMappings[$field];
                $columns[$displayName] = $callback ?? $field;
            }
        }

        $title = str_replace('_', ' ', $request->report_type);

        $meta = [
            'Datetime' => Carbon::now(),
            'Sort by' => $request->sort_by,
            'Sort order' => $request->sort_order,
            'Librarian' => auth()->user()->last_name . ', ' . auth()->user()->first_name
        ];

        $copies = \App\Models\Copy::select()
            ->with('collection.authors:id,author', 'collection:id,title,barcode');

        if ($request->sort_by == 'title') {
            $copies = $copies->join('collections', 'copies.collection_id', '=', 'collections.id')
                ->select('copies.*', 'collections.title')
                ->orderBy('collections.' .  $request->sort_by,  $request->sort_order);
        } else {
            $copies = $copies->orderBy($request->sort_by,  $request->sort_order);
        }

        if ($request->report_type == 'Available_Copies_List') {
            $copies = $copies->where('availability', 'available');
        }

        if ($request->report_type == 'On_Loan_Copies_List') {
            $copies = $copies->where('availability', 'on loan');
        }

        if ($request->report_type == 'Reserved_Copies_List') {
            $copies = $copies->where('availability', 'reserved');
        }

        if ($request->acquired_at_start && $request->acquired_at_end) {
            $start = Carbon::parse($request->acquired_at_start);
            $end = Carbon::parse($request->acquired_at_end);

            if ($start == $end) {
                $copies = $copies->whereDate('acquired_at', $start);
            } else {
                $copies = $copies->whereBetween('acquired_at', [$start->subDays(1), $end->addDays(1)]);
            }
        }

        if ($request->created_at_start && $request->created_at_end) {
            $start = Carbon::parse($request->created_at_start);
            $end = Carbon::parse($request->created_at_end);

            if ($start == $end) {
                $copies = $copies->whereDate('created_at', $start);
            } else {
                $copies = $copies->whereBetween('created_at', [$start->subDays(1), $end->addDays(1)]);
            }
        }

        $directoryPath = 'public/reports';
        $fileName = $request->report_type . '_' . now()->format('Y-m-d_H-i-s');

        Report::create([
            'report_type' => str_replace('_', ' ', $request->report_type),
            'fields' => implode(', ', $request->fields),
            'sort_by' => $request->sort_by,
            'sort_order' => $request->sort_order,
            'file_type' => Str::title($request->file_type),
            'file_name' => $fileName . ($request->file_type == 'pdf' ? '.pdf' : '.xlsx'),
            'librarian_id' => auth()->user()->id
        ]);

        if (!Storage::disk('public')->exists($directoryPath)) {
            Storage::disk('public')->makeDirectory($directoryPath);
        }

        if ($request->file_type == 'pdf') {
            $pdf = PdfReportFacade::of($title, $meta, $copies, $columns)
                ->setOrientation('landscape');

            if (!empty($request->limit)) {
                $pdf->limit($request->limit);
            }

            $pdf->store("{$directoryPath}/{$fileName}.pdf");
        } elseif ($request->file_type == 'excel') {
            $excel = ExcelReportFacade::of($title, $meta, $copies, $columns)
                ->simple();

            if (!empty($request->limit)) {
                $excel->limit($request->limit);
            }

            $excel->store("{$directoryPath}/{$fileName}.xlsx");
        }
    }

    public function offSiteCirculationsList(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $columnMappings = [
            'title' => ['Title', function ($circulation) {
                return $circulation->copy->collection->title;
            }],
            'barcode' => ['Barcode', function ($circulation) {
                return $circulation->copy->barcode;
            }],
            'checked_out_at' => ['Checked-Out At', null],
            'checked_in_at' => ['Checked-In At', null],
            'due_at' => ['Due At', function ($copy) {
                return $copy->due_at->format('Y-m-d');
            }],
            'fines_status' => ['Fines Status', null],
            'total_fines' => ['Total Fines', null],
            'borrower' => ['Borrower', function ($circulation) {
                return $circulation->borrower->last_name . ', ' . $circulation->borrower->first_name;
            }],
            'created_at' => ['Created At', function ($copy) {
                return $copy->created_at->format('Y-m-d h:i A');
            }]
        ];

        $columns = [];

        foreach ($request->fields as $field) {
            if (isset($columnMappings[$field])) {
                [$displayName, $callback] = $columnMappings[$field];
                $columns[$displayName] = $callback ?? $field;
            }
        }

        $title = str_replace('_', ' ', $request->report_type);

        $meta = [
            'Datetime' => Carbon::now(),
            'Sort by' => $request->sort_by,
            'Sort order' => $request->sort_order,
            'Librarian' => auth()->user()->last_name . ', ' . auth()->user()->first_name
        ];

        $circulations = \App\Models\OffSiteCirculation::select()
            ->with('copy:id,barcode,collection_id', 'copy.collection:id,title', 'borrower:id,last_name,first_name')
            ->orderBy($request->sort_by,  $request->sort_order);

        if ($request->report_type == 'On_Loan_Off-Site_Circulations_List') {
            $circulations = $circulations->whereNull('checked_in_at');
        }

        if ($request->report_type == 'Outstanding_Off-Site_Circulations_List') {
            $circulations = $circulations->whereNull('checked_in_at')
                ->where('due_at', '<', Carbon::now()->toDateString());
        }

        if ($request->acquired_at_start && $request->acquired_at_end) {
            $start = Carbon::parse($request->acquired_at_start);
            $end = Carbon::parse($request->acquired_at_end);

            if ($start == $end) {
                $circulations = $circulations->whereDate('acquired_at', $start);
            } else {
                $circulations = $circulations->whereBetween('acquired_at', [$start->subDays(1), $end->addDays(1)]);
            }
        }

        if ($request->created_at_start && $request->created_at_end) {
            $start = Carbon::parse($request->created_at_start);
            $end = Carbon::parse($request->created_at_end);

            if ($start == $end) {
                $circulations = $circulations->whereDate('created_at', $start);
            } else {
                $circulations = $circulations->whereBetween('created_at', [$start->subDays(1), $end->addDays(1)]);
            }
        }

        $directoryPath = 'public/reports';
        $fileName = $request->report_type . '_' . now()->format('Y-m-d_H-i-s');

        Report::create([
            'report_type' => str_replace('_', ' ', $request->report_type),
            'fields' => implode(', ', $request->fields),
            'sort_by' => $request->sort_by,
            'sort_order' => $request->sort_order,
            'file_type' => Str::title($request->file_type),
            'file_name' => $fileName . ($request->file_type == 'pdf' ? '.pdf' : '.xlsx'),
            'librarian_id' => auth()->user()->id
        ]);

        if (!Storage::disk('public')->exists($directoryPath)) {
            Storage::disk('public')->makeDirectory($directoryPath);
        }

        if ($request->file_type == 'pdf') {
            $pdf = PdfReportFacade::of($title, $meta, $circulations, $columns)
                ->setOrientation('landscape');

            if (!empty($request->limit)) {
                $pdf->limit($request->limit);
            }

            $pdf->store("{$directoryPath}/{$fileName}.pdf");
        } elseif ($request->file_type == 'excel') {
            $excel = ExcelReportFacade::of($title, $meta, $circulations, $columns)
                ->simple();

            if (!empty($request->limit)) {
                $excel->limit($request->limit);
            }

            $excel->store("{$directoryPath}/{$fileName}.xlsx");
        }
    }

    public function inHouseCirculationsList(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $columnMappings = [
            'title' => ['Title', function ($circulation) {
                return $circulation->copy->collection->title;
            }],
            'barcode' => ['Barcode', function ($circulation) {
                return $circulation->copy->barcode;
            }],
            'librarian' => ['Librarian', function ($circulation) {
                return $circulation->librarian->last_name . ', ' . $circulation->librarian->first_name;
            }],
            'created_at' => ['Created At', function ($copy) {
                return $copy->created_at->format('Y-m-d h:i A');
            }]
        ];

        $columns = [];

        foreach ($request->fields as $field) {
            if (isset($columnMappings[$field])) {
                [$displayName, $callback] = $columnMappings[$field];
                $columns[$displayName] = $callback ?? $field;
            }
        }

        $title = str_replace('_', ' ', $request->report_type);

        $meta = [
            'Datetime' => Carbon::now(),
            'Sort by' => $request->sort_by,
            'Sort order' => $request->sort_order,
            'Librarian' => auth()->user()->last_name . ', ' . auth()->user()->first_name
        ];

        $circulations = \App\Models\InHouseCirculation::with('librarian:id,first_name,last_name')
            ->orderBy($request->sort_by,  $request->sort_order);

        if ($request->created_at_start && $request->created_at_end) {
            $start = Carbon::parse($request->created_at_start);
            $end = Carbon::parse($request->created_at_end);

            if ($start == $end) {
                $circulations = $circulations->whereDate('created_at', $start);
            } else {
                $circulations = $circulations->whereBetween('created_at', [$start->subDays(1), $end->addDays(1)]);
            }
        }

        $directoryPath = 'public/reports';
        $fileName = $request->report_type . '_' . now()->format('Y-m-d_H-i-s');

        Report::create([
            'report_type' => str_replace('_', ' ', $request->report_type),
            'fields' => implode(', ', $request->fields),
            'sort_by' => $request->sort_by,
            'sort_order' => $request->sort_order,
            'file_type' => Str::title($request->file_type),
            'file_name' => $fileName . ($request->file_type == 'pdf' ? '.pdf' : '.xlsx'),
            'librarian_id' => auth()->user()->id
        ]);

        if (!Storage::disk('public')->exists($directoryPath)) {
            Storage::disk('public')->makeDirectory($directoryPath);
        }

        if ($request->file_type == 'pdf') {
            $pdf = PdfReportFacade::of($title, $meta, $circulations, $columns)
                ->setOrientation('landscape');

            if (!empty($request->limit)) {
                $pdf->limit($request->limit);
            }

            $pdf->store("{$directoryPath}/{$fileName}.pdf");
        } elseif ($request->file_type == 'excel') {
            $excel = ExcelReportFacade::of($title, $meta, $circulations, $columns)
                ->simple();

            if (!empty($request->limit)) {
                $excel->limit($request->limit);
            }

            $excel->store("{$directoryPath}/{$fileName}.xlsx");
        }
    }


    public function downloadLink($id)
    {
        $report = Report::findOrFail($id);

        return response()->json(asset('storage/reports/' . $report->file_name));
    }

    public function destroy(Request $request)
    {
        $message = 'Report';

        if (is_array($request->id)) {
            Report::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            Report::findOrFail($request->id)->delete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function restore(Request $request)
    {
        $message = 'Report';

        if (is_array($request->id)) {
            Report::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            Report::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Report';

        if (is_array($request->id)) {
            Report::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            Report::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }
}
