<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Models\InHouseCirculation;

class InHouseCirculationController extends Controller
{
    private function button($display, $id, $className, $size = 'lg', $disabled = false)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-' . $size . ' btn-in-house-circulation-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '" ' . ($disabled == true ? 'disabled style="background: none !important;"' : '') . '>' . $display . '</button>';
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('in-house-circulations.index');
        }

        $inHouseCirculations = InHouseCirculation::with(['librarian:id,first_name,last_name', 'copy:id,barcode,collection_id', 'copy.collection:id,title'])->orderBy('updated_at', 'desc');

        if (Route::currentRouteName() == 'in.house.circulations.archive') {
            $inHouseCirculations = $inHouseCirculations->onlyTrashed();
        }

        return Datatables::of($inHouseCirculations)
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                if (auth()->user()->temp_role == 'librarian' & Route::currentRouteName() == 'in.house.circulations.index') {
                    $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'delete');
                }

                if (Route::currentRouteName() == 'in.house.circulations.archive') {
                    $html .= $this->button('<i class="fa fa-undo"></i>', $row->id, 'restore');
                    $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'force-delete');
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->addColumn('barcode', function ($row) {
                return '<a href="' . route('collections.index') . '/' . $row->copy->collection_id . '">' . $row->copy->barcode . '</a>';
            })
            ->filterColumn('barcode', function ($query, $keyword) {
                $query->whereHas('copy', function ($q) use ($keyword) {
                    $q->where('barcode', 'like', '%' . $keyword . '%');
                });
            })
            ->addColumn('title', function ($row) {
                return '<a href="' . route('collections.index') . '/' . $row->copy->collection_id . '">' . $row->copy->collection->title . '</a>';
            })
            ->filterColumn('title', function ($query, $keyword) {
                $query->whereHas('copy.collection', function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%');
                });
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
            ->addColumn('date', function ($row) {
                return $row->created_at->format('Y-m-d g:i A');
            })
            ->rawColumns(['action', 'checkbox', 'barcode', 'librarian', 'title'])
            ->make(true);
    }

    public function store($barcode)
    {
        $copy = \App\Models\Copy::where('barcode', $barcode)->first();

        if ($copy == null) {
            return response()->json(['error' => 'Barcode does not exist.']);
        }

        $copy->inHouseCirculations()->create([
            'librarian_id' => auth()->user()->id
        ]);

        return response()->json(['success' => 'In-house record successfully created!']);
    }

    public function destroy(Request $request)
    {
        $message = 'In-house circulation';

        if (is_array($request->id)) {
            InHouseCirculation::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            InHouseCirculation::findOrFail($request->id)->delete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function restore(Request $request)
    {
        $message = 'In-house circulation';

        if (is_array($request->id)) {
            InHouseCirculation::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            InHouseCirculation::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'In-house circulation';

        if (is_array($request->id)) {
            InHouseCirculation::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            InHouseCirculation::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }
}
