<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Models\OffSiteCirculation;
use Illuminate\Support\Facades\Validator;

class OffSiteCirculationController extends Controller
{

    private function button($display, $id, $className, $disabled = false)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-off-site-circulation-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '" ' . ($disabled == true ? 'disabled style="background: none !important;"' : '') . '>' . $display . '</button>';
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            $enableAutomaticFines = \App\Models\Setting::where('field', 'enable_automatic_fines')->first();

            return view('off-site-circulations.index')
                ->with('enableAutomaticFines', $enableAutomaticFines->value);
        }

        $offSiteCirculations = OffSiteCirculation::with(['copy:id,barcode,collection_id', 'copy.collection:id,title,format', 'borrower:id,last_name,first_name'])
            ->select('id', 'copy_id', 'due_at', 'borrower_id', 'checked_in_at', 'created_at', 'updated_at', 'total_fines', 'status', 'fines_status', 'grace_period_days')
            ->orderBy('updated_at', 'desc');

        if (auth()->user()->temp_role != 'librarian') {
            $offSiteCirculations = $offSiteCirculations->where('borrower_id', auth()->user()->id)->select();
        }

        $offSiteCirculations = $offSiteCirculations->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'off.site.circulations.archive') {
            $offSiteCirculations = $offSiteCirculations->onlyTrashed();
        }

        return Datatables::of($offSiteCirculations)
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                $html .= '<a href="' . route('off.site.circulations.show', ['id' => $row->id]) . '">' . $this->button('<i class="fa fa-eye"></i>', $row->id, 'view') . '</a>';

                if (auth()->user()->temp_role == 'librarian' && Route::currentRouteName() == 'off.site.circulations.index' && ($row->status == 'checked-in' || $row->status == 'lost') && !($row->total_fines > 0 && $row->fines_status == 'unpaid')) {
                    $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'delete');
                } else if (auth()->user()->temp_role != 'librarian' && Route::currentRouteName() == 'off.site.circulations.index') {
                    $paymentSubmitted = $row->payments()->where('status', 'pending')->exists();

                    if ($row->total_fines > 0 & $row->fines_status != 'paid' && !$paymentSubmitted && $row->status != 'checked-out') {
                        $html .= $this->button('Send Payment', $row->id, 'send-payment');
                    }
                }

                if (Route::currentRouteName() == 'off.site.circulations.archive') {
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
            ->addColumn('checked_out', function ($row) {
                return $row->created_at->format('Y-m-d g:i A');
            })
            ->filterColumn('checked_out', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') like ?", ["%$keyword%"]);
            })
            ->addColumn('checked_in_at', function ($row) {
                return optional($row->checked_in_at)->format('Y-m-d g:i A');
            })
            ->filterColumn('checked_in_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(checked_in_at, '%Y-%m-%d %h:%i %p') like ?", ["%$keyword%"]);
            })
            ->addColumn('due_at', function ($row) {
                if (!$row->checked_in_at && $row->due_at) {
                    $today = Carbon::today()->setTime(8, 0, 0);
                    $dueAtDate = Carbon::parse($row->due_at)->startOfDay();
                
                    if ($dueAtDate->lte($today)) {
                        return '<span class="text-danger">' . optional($row->due_at)->format('Y-m-d g:i A') . '</span>';
                    } 
                }
                return optional($row->due_at)->format('Y-m-d g:i A');
            })
            ->filterColumn('due_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(due_at, '%Y-%m-%d %h:%i %p') like ?", ["%$keyword%"]);
            })
            ->addColumn('total_fines', function ($row) {
                if ($row->total_fines == 0) {
                    return;
                }

                return '<div class="' . ($row->fines_status == 'unpaid' ? 'text-danger font-weight-bold' : '') . '">₱ ' . number_format($row->total_fines, 2, '.', ',') . '</div>';
            })
            ->addColumn('status', function ($row) {
                $array = [
                    'checked-out' => 'warning',
                    'checked-in' => 'primary',
                    'lost' => 'danger'
                ];

                return '<span class="badge badge-' . $array[$row->status] . '">' . $row->status . '</span>';
            })
            ->addColumn('disabled', function ($row) {
                return !($row->status == 'checked-in' || $row->status == 'lost') || ($row->total_fines > 0 && $row->fines_status == 'unpaid') ? true : false;
            })
            ->rawColumns(['action', 'checkbox', 'barcode', 'checked_out', 'checked_in_at', 'due_at', 'total_fines', 'status'])
            ->make(true);
    }

    public function show($id)
    {
        $offSiteCirculation = OffSiteCirculation::withTrashed()->with('borrower:id,last_name,first_name', 'librarian:id,last_name,first_name', 'copy:id,barcode,collection_id')->find($id);

        return view('off-site-circulations.show')
            ->with([
                'offSiteCirculation' => $offSiteCirculation,
                'offSiteCirculationStatus' => $offSiteCirculation->deleted_at == null ? 'active' : 'archived',
            ]);
    }

    public function create()
    {
        auth()->user()->tempCheckOutItems()->delete();
        return view('off-site-circulations.create');
    }

    public function store(Request $request) //CHECK OUT
    {
        $borrower = \App\Models\Patron::where('id2', $request->id2)->first();

        $tempCheckOutItems = \App\Models\TempCheckOutItem::where('librarian_id', auth()->user()->id)->get();

        foreach ($tempCheckOutItems as $item) {
            $copy = $item->copy;

            $offSiteCirculation = $copy->offSiteCirculations()->create([
                'librarian_id' => auth()->user()->id,
                'borrower_id' => $borrower->id,
                'due_at' => $item->due_at,
                'grace_period_days' => $item->grace_period_days,
                'checked_out_at' => Carbon::now(),
                'status' => 'checked-out'
            ]);

            if ($copy->availability == 'reserved') {
                $reservation = $copy->reservations()
                    ->where('borrower_id', $borrower->id)
                    ->where('status', 'ready for check-out')
                    ->whereDate('check_out_before', '>=', now())
                    ->first();

                $reservation->update(['status' => 'checked-out']);
                $offSiteCirculation->update(['reservation_id' => $reservation->id]);
            }

            $copy->update(['availability' => 'on loan']);
        }

        $tempCheckOutItems->each->delete();

        session()->flash('success', 'Check-out circulation has been successfully created!');
        return back();
    }

    public function updateFinesStatus(Request $request, $id)
    {
        OffSiteCirculation::findOrFail($id)->update(['fines_status' => $request->status]);
    }

    public function checkIn($barcode)
    {
        $copy = \App\Models\Copy::where('barcode', $barcode)->first();

        if ($copy == null) {
            return response()->json(['error' => 'Barcode does not exist.']);
        }

        $offSiteCirculation = OffSiteCirculation::where('copy_id', $copy->id)->latest()->first();

        if ($offSiteCirculation == null) {
            return response()->json(['error' => 'No circulation records found related to this copy.']);
        }

        if ($offSiteCirculation->checked_in_at == null) {
            $offSiteCirculation->checked_in_at = Carbon::now();
            $offSiteCirculation->status = 'checked-in';
            $offSiteCirculation->save();

            $copyLatestReservation = $copy->reservations()
                ->where('status', 'pending')
                ->oldest()
                ->first();

            if ($copyLatestReservation != null) {
                $copy->availability = 'reserved';
                $copyLatestReservation->update(
                    [
                        'check_out_before' => Carbon::now()->addDays(3)->startOfDay(),
                        'status' => 'ready for check-out'
                    ]
                );
            } else {
                $copy->availability = 'available';
            }

            $copy->save();

            return response()->json(['success' => 'Copy has been successfully checked-in!']);
        } else {
            return response()->json(['error' => 'Copy is not currently checked-out.']);
        }
    }

    public function getDueAt($barcode)
    {
        $copy = \App\Models\Copy::where('barcode', $barcode)->first();

        if ($copy == null) {
            return response()->json(['error' => 'Barcode does not exist.']);
        }

        $offSiteCirculation = OffSiteCirculation::where('copy_id', $copy->id)
            ->whereNull('checked_in_at')
            ->latest()
            ->first();

        if ($offSiteCirculation == null) {
            return response()->json(['error' => 'Copy is not currently checked-out.']);
        }

        return response()->json([
            'due_at' => $offSiteCirculation->due_at->addDays(1)->toDateString(),
            'barcode' => $barcode
        ]);
    }

    public function renew(Request $request, $barcode)
    {
        $validated = Validator::make($request->all(), [
            'new_due_at' => ['required', 'date']
        ]);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $copy = \App\Models\Copy::where('barcode', $barcode)->first();

        $offSiteCirculation = $copy->offSiteCirculations()
            ->where('checked_in_at', null)
            ->latest()
            ->first();

        if ($offSiteCirculation == null) {
            return response()->json(['error' => 'Item is currently not checked-out.']);
        }

        $offSiteCirculation->renewals()->create([
            'old_due_at' => $offSiteCirculation->due_at,
            'new_due_at' => Carbon::parse($request->new_due_at)->setTime(8, 0, 0),
            'librarian_id' => auth()->user()->id
        ]);

        $offSiteCirculation->update([
            'due_at' => Carbon::parse($request->new_due_at)->setTime(8, 0, 0)
        ]);

        return response()->json(['success' => 'Circulation has been successfully renewed!']);
    }

    public function destroy(Request $request)
    {
        $message = 'Off-site circulation';
        $errorCount = 0;
        $successCount = 0;

        if (is_array($request->id)) {
            $circulations = OffSiteCirculation::whereIn('id', $request->id)->get();

            foreach ($circulations as $circulation) {
                if ($this->deleteCirculation($circulation) == 'error') {
                    $errorCount++;
                } else {
                    $successCount++;
                }
            }

            $message .= 's have ';
        } else {
            if ($this->deleteCirculation(OffSiteCirculation::findOrFail($request->id)) == 'error') {
                return response()->json(['error' => 'You cannot delete checked-out circulation!']);
            };
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';

        if ($successCount == 0 && is_array($request->id)) {
            return response()->json(['error' => 'Off-site circulations failed to be deleted!']);
        }

        return response()->json(['success' => $message . ($errorCount > 0 ? ' ' . $errorCount . ' failed.' : ' ')]);
    }

    public function deleteCirculation(OffSiteCirculation $circulation)
    {
        if (!$circulation->checked_in_at) {
            return 'error';
        }

        $circulation->delete();
    }

    public function restore(Request $request)
    {
        $message = 'Off-site circulation';

        if (is_array($request->id)) {
            OffSiteCirculation::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            OffSiteCirculation::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Off-site circulation';

        if (is_array($request->id)) {
            OffSiteCirculation::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            OffSiteCirculation::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }

    public function markAsLost($id)
    {
        $offSiteCirculation = OffSiteCirculation::find($id);
        $copy = $offSiteCirculation->copy()->first();

        $offSiteCirculation->update([
            'status' => 'lost'
        ]);

        $offSiteCirculation->copy()->update([
            'availability' => 'lost'
        ]);

        $offSiteCirculation->fines()->create([
            'reason' => 'Lost Book Fee',
            'price' => $copy->price,
            'librarian_id' => auth()->user()->id
        ]);

        $offSiteCirculation->total_fines = $offSiteCirculation->fines()->sum('price');
        $offSiteCirculation->save();

        return response()->json(['success' => 'Copy has been successfully marked as lost!']);
    }

    public function UndoMarkAsLost($id)
    {
        $offSiteCirculation = OffSiteCirculation::find($id);

        $offSiteCirculation->update([
            'status' => 'checked-out'
        ]);

        $offSiteCirculation->copy()->update([
            'availability' => 'on loan'
        ]);

        $offSiteCirculation->fines()->where('reason', 'Lost Book Fee')->first()->delete();

        $offSiteCirculation->total_fines = $offSiteCirculation->fines()->sum('price');
        $offSiteCirculation->save();


        return response()->json(['success' => 'Lost status for the copy has been removed!']);
    }
}
