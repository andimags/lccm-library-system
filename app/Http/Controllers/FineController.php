<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Models\Fine;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class FineController extends Controller
{
    private function rules(Request $request, Fine $fine = null)
    {
        $rules = [
            'reason' => ['required'],
            'note' => ['nullable'],
            'price' => ['required', 'regex:/^(\d{1,3}(,\d{3})*|(\d+))(\.\d{2})?$/'],
        ];

        $validator = Validator::make($request->all(), $rules);

        return $validator;
    }

    private function button($display, $id, $className, $size = 'lg', $disabled = false)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-' . $size . ' btn-off-site-circulation-fine-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '" ' . ($disabled == true ? 'disabled style="background: none !important;"' : '') . '>' . $display . '</button>';
    }

    public function index($circulation_id)
    {
        $fines = \App\Models\OffSiteCirculation::withTrashed()
            ->find($circulation_id)
            ->fines()
            ->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'fines.archive') {
            $fines = $fines->onlyTrashed();
        }

        return Datatables::of($fines)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                if (auth()->user()->temp_role == 'librarian' & Route::currentRouteName() == 'fines.index') {
                    $html .= $this->button('<i class="fa fa-edit"></i>', $row->id, 'edit');
                    $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'delete');
                    // $html .= $this->button('Renew', $row->id, 'renew', 'sm');
                }

                if (Route::currentRouteName() == 'fines.archive') {
                    // $html .= $this->button('<i class="fa fa-undo"></i>', $row->id, 'restore');
                    $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'force-delete');
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d g:i A');
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') like ?", ["%$keyword%"]);
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->addColumn('librarian', function ($row) {
                if ($row->librarian_id) {
                    return '<a href="' . route("patrons.index") . '/' . $row->librarian_id . '">' . $row->librarian->last_name . ', ' . $row->librarian->first_name . '</a>';
                }
            })
            ->filterColumn('librarian', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('librarian', function ($subquery) use ($keyword) {
                        $subquery->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                });
            })
            ->addColumn('price', function ($row) {
                return '₱ ' . $row->price;
            })
            ->rawColumns(['action', 'checkbox', 'librarian'])
            ->make(true);
    }

    public function store(Request $request, $circulation_id)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $offSiteCirculation = \App\Models\OffSiteCirculation::findOrFail($circulation_id);

        DB::transaction(function () use ($offSiteCirculation, $request) {
            try {
                $offSiteCirculation->fines()->create([
                    'reason' => $request->reason,
                    'note' => $request->note,
                    'price' => str_replace(',', '', $request->price),
                    'librarian_id' => auth()->user()->id
                ]);

                $offSiteCirculation->total_fines = $offSiteCirculation->fines()->sum('price');
                $offSiteCirculation->save();
            } catch (\Exception $e) {
                return response()->json($e);
            }
        });

        return response()->json(['success' => 'Fine has been successfully added!']);
    }

    public function edit($id)
    {
        $fine = Fine::find($id);

        return response()->json($fine);
    }

    public function update(Request $request, $id)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $fine = Fine::findOrFail($id);

        DB::transaction(function () use ($fine, $request) {
            $fine->price = $request->price;
            $fine->save();

            $circulation = $fine->offSiteCirculation()->first();

            $circulation->update([
                'total_fines' => $circulation->fines()->sum('price'),
            ]);
        });

        $fine->update([
            'reason' => $request->reason,
            'note' => $request->note
        ]);

        return response()->json(['success' => 'Fine has been successfully updated!']);
    }

    public function destroy(Request $request)
    {
        $message = 'Fine';

        if (is_array($request->id)) {
            $fines = Fine::whereIn('id', $request->id)->get();

            foreach ($fines as $fine) {
                $this->deleteFine($fine);
            }

            $message .= 's have ';
        } else {
            $this->deleteFine(Fine::findOrFail($request->id));
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function deleteFine(Fine $fine)
    {
        $circulation = $fine->offSiteCirculation()->first();
        $fine->delete();
        $circulation->update([
            'total_fines' => $circulation->fines()->sum('price'),
        ]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Fine';

        if (is_array($request->id)) {
            $fines = Fine::withTrashed()->whereIn('id', $request->id)->get();

            foreach ($fines as $fine) {
                $this->forceDeleteFine($fine);
            }

            $message .= 's have ';
        } else {
            $this->forceDeleteFine(Fine::withTrashed()->findOrFail($request->id));
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }

    public function forceDeleteFine(Fine $fine)
    {
        $circulation = $fine->offSiteCirculation()->withTrashed()->first();
        $fine->forceDelete();
        $circulation->update([
            'total_fines' => $circulation->fines()->sum('price'),
            'fines_status' => $circulation->fines()->sum('price') > 0 ? $circulation->fines_status : null
        ]);
    }
}
