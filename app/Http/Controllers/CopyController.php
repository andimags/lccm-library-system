<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Copy;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class CopyController extends Controller
{
    public function rules(Request $request, Copy $copies = null)
    {
        $rules = [
            'barcode' => ['required', 'string', 'max:10', Rule::unique('copies')->ignore($copies)],
            'price' => ['nullable', 'numeric', 'min:0'],
            'fund' => ['nullable', 'in:donated,purchased'],
            'call_prefix' => ['nullable'],
            'vendor' => ['nullable'],
            'date_acquired' => ['nullable', 'date'],
        ];

        $validator = Validator::make($request->all(), $rules);

        return $validator;
    }

    private function button($value, $id, $className, $btnSize = 'lg', $disabled = false)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-' . $btnSize . ' btn-copy-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '" ' . ($disabled == true ? 'disabled style="background: none !important;"' : '') . '>' . $value . '</button>';
    }

    public function index($id)
    {
        $copies = \App\Models\Collection::withTrashed()
            ->findOrFail($id)
            ->copies()
            ->with(['librarian'])
            ->select()
            ->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'copies.archive') {
            $copies = $copies->onlyTrashed();
        }

        return Datatables::of($copies)
            ->addColumn('action', function ($row) {
                if(!auth()->check()){
                    return;
                }
                
                $html = '<td> <div class="form-button-action">';

                if (auth()->user()->temp_role == 'librarian' & Route::currentRouteName() == 'copies.index') {
                    $html .= $this->button('<i class="fa fa-edit"></i>', $row->id, 'edit');

                    if(!($row->availability == 'on loan' || $row->availability == 'reserved')){
                        $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'delete');
                    }
                } else if (auth()->user()->temp_role != 'librarian' & Route::currentRouteName() == 'copies.index') {
                    // CHECK IF CURRENT LOGGED IN PATRON ALREADY RESERVED THE COPY
                    $isReserved = $row->reservations()
                    ->where('borrower_id', auth()->user()->id)
                    ->where(function ($query) {
                        $query->where('status', 'pending')
                              ->orWhere('status', 'ready for check-out');
                    })
                    ->exists();

                    // CHECK IF THE CURRENT LOGGED IN USER ALREADY BORROWED THE COPY
                    $isBorrowed = $row->offSiteCirculations()
                        ->whereNull('checked_in_at')
                        ->where('borrower_id', auth()->user()->id)
                        ->exists();

                    $html .= $this->button(
                        'Reserve',
                        $row->id,
                        'reserve',
                        '',
                        ($row->availability == 'available' || $isReserved || $isBorrowed ? true : false)
                    );

                    // CHECK IF COPY IS ALREADY ON PATRON'S SHELF
                    $isOnShelf = \App\Models\ShelfItem::where('borrower_id', auth()->user()->id)
                        ->where('copy_id', $row->id)
                        ->exists();

                    $html .= $this->button(
                        'Add to shelf',
                        $row->id,
                        'shelf',
                        '',
                        ($row->availability == 'available' || $isReserved || $isOnShelf || $isBorrowed ? true : false)
                    );
                }

                // if (Route::currentRouteName() == 'copies.archive') {
                //     $html .= $this->button('<i class="fa fa-undo"></i>', $row->id, 'restore');
                //     $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'force-delete');
                // }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->addColumn('librarian', function ($row) {
                if ($row->librarian_id) {
                    return $row->librarian->last_name . ', ' . $row->librarian->first_name;
                }
            })
            ->addColumn('availability', function ($row) {
                $array = [
                    'available' => 'success',
                    'on loan' => 'warning',
                    'reserved' => 'warning',
                    'lost' => 'danger'
                ];

                return '<span class="badge badge-' . $array[$row->availability] . '">' . $row->availability . '</span>';
            })
            ->addColumn('disabled', function ($row) {
                return $row->availability == 'on loan' || $row->availability == 'reserved';
            })
            ->rawColumns(['action', 'checkbox', 'librarian', 'availability'])
            ->make(true);
    }

    public function store(Request $request, $id)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        Copy::create([
            'librarian_id' => auth()->user()->id,
            'collection_id' => $id,
            'barcode' => $request->barcode,
            'price' => $request->price,
            'fund' => $request->fund,
            'vendor' => $request->vendor,
            'call_prefix' => $request->call_prefix,
            'date_acquired' => $request->date_acquired,
        ]);

        $collection = \App\Models\Collection::findOrFail($id);
        $collection->total_copies = $collection->copies()->count();
        $collection->save();

        return response()->json(['success' => 'Copy has been added successfully.']);
    }

    public function edit($id)
    {
        $copy = Copy::findOrFail($id);

        return response()->json($copy);
    }

    public function update(Request $request, $id)
    {
        $copy = Copy::where('id', $id)->first();

        $validated = $this->rules($request, $copy);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $copy->barcode = $request->barcode;
        $copy->fund = $request->fund;
        $copy->vendor = $request->vendor;
        $copy->price = $request->price;
        $copy->date_acquired = $request->date_acquired;
        $copy->call_prefix = $request->call_prefix;

        $copy->save();

        return response()->json(['success' => 'Copy has been updated successfully.']);
    }

    public function get($id)
    {
        $copy = Copy::with('collection:id,title')
            ->select('barcode', 'collection_id')
            ->findOrFail($id);

        return dd($copy);
    }

    public function search($barcode)
    {
        $copies = \App\Models\Copy::with(['collection:id,title'])
            ->select('id', 'collection_id', 'barcode')
            ->where('barcode', 'LIKE', $barcode . '%')
            ->take(50)
            ->get();

        return response()->json($copies);
    }

    public function destroy(Request $request)
    {
        $message = '';

        if (is_array($request->id)) {
            Copy::whereIn('id', $request->id)->get()->each->delete();
            $message .= 'Copies have ';
        } else {
            Copy::findOrFail($request->id)->delete();
            $message .= 'Copy has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function restore(Request $request)
    {
        $message = '';

        if (is_array($request->id)) {
            Copy::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 'Copies have ';
        } else {
            Copy::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= 'Copy has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = '';

        if (is_array($request->id)) {
            Copy::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 'Copies have ';
        } else {
            Copy::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= 'Copy has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }

    public function checkUniqueness(Request $request)
    {
        $isUnique = true;

        if ($request->action == 'add') {
            $isUnique = !Copy::where($request->field, $request->value)
                ->exists();
        } else if ($request->action == 'edit') {
            $currentCopy = Copy::find($request->copy_id);

            if ($currentCopy->{$request->field} != $request->value) {
                $isUnique = !Copy::where($request->field, $request->value)
                    ->where('id', '<>', $currentCopy->id)
                    ->exists();
            }
        }

        return response()->json($isUnique);
    }
}
