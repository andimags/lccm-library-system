<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;

class ReservationController extends Controller
{
    // STATUSES
    // 'pending', 'ready for check-out', 'checked-out', 'canceled'

    private function button($value, $id, $className, $disabled = false)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-reservation-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '" ' . ($disabled == true ? 'disabled style="background: none !important;"' : '') . '>' . $value . '</button>';
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('reservations.index');
        }

        $reservations = Reservation::with(['copy:id,barcode,collection_id,availability', 'copy.collection:id,title', 'copy.collection.images:id,file_name,imageable_id,imageable_type', 'borrower:id,first_name,last_name']);

        if (auth()->user()->temp_role != 'librarian') {
            $reservations = $reservations->where('borrower_id', auth()->user()->id)->select();
        }

        $reservations = $reservations->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'reservations.archive') {
            $reservations = $reservations->onlyTrashed();
        }

        return Datatables::of($reservations)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                if ($row->status != 'canceled' & $row->status != 'checked-out') {
                    $html .= $this->button('Cancel', $row->id, 'cancel');
                }

                if (auth()->user()->temp_role == 'librarian' && Route::currentRouteName() == 'reservations.index' && $row->status != 'ready for check-out') {
                    $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'delete');
                }

                if (Route::currentRouteName() == 'reservations.archive') {
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
                return '<a href="' . route('collections.index') . '/' . $row->copy->collection_id . '">' . Str::limit($row->copy->collection->title, 25, '...') . '</a>';
            })
            ->filterColumn('title', function ($query, $keyword) {
                $query->whereHas('copy.collection', function ($q) use ($keyword) {
                    $q->where('title', 'like', '%' . $keyword . '%');
                });
            })
            ->addColumn('status', function ($row) {
                $array = [
                    'pending' => 'count',
                    'ready for check-out' => 'success',
                    'canceled' => 'warning',
                    'checked-out' => 'info'
                ];

                return '<span class="badge badge-' . $array[$row->status] . '">' . $row->status . '</span>';
            })
            ->addColumn('image', function ($row) {
                $image = $row->copy->collection->images->first();
                $file_name = $image->file_name ?? 'default.jpg';

                return '<div class="avatar avatar-xl"><img src="' . asset('/images/collections/' . $file_name) . '" class="avatar-img rounded"></div>';
            })
            ->addColumn('check_out_before', function ($row) {
                return optional($row->check_out_before)->format('F j, Y');
            })
            ->addColumn('borrower', function ($row) {
                return '<a href="' . route("patrons.index") . '/' . $row->borrower_id . '">' . $row->borrower->last_name . ', ' . $row->borrower->first_name . '</a>';
            })
            ->filterColumn('borrower', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('borrower', function ($subquery) use ($keyword) {
                        $subquery->where('first_name', 'like', '%' . $keyword . '%')
                            ->orWhere('last_name', 'like', '%' . $keyword . '%');
                    });
                });
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('status', 'like', '%' . $keyword . '%');
                });
            })
            ->addColumn('created_at', function ($row) {
                return optional($row->created_at)->format('Y-m-d g:i A');
            })
            ->addColumn('disabled', function ($row) {
                return $row->status == 'ready for check-out' ? true : false;
            })
            ->rawColumns(['action', 'checkbox', 'status', 'image', 'barcode', 'title', 'borrower'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $message = 'Reservation';

        // RESERVE DIRECTLY
        if ($request->copyId) {
            $copy = \App\Models\Copy::findOrFail($request->copyId);
            $copy->reservations()->create([
                'borrower_id' => auth()->user()->id,
            ]);

            $message .= ' has been successfully created!';
            return response()->json(['success' => $message]);
        }

        if (is_array($request->id)) {
            $ShelfItems = \App\Models\ShelfItem::whereIn('id', $request->id)->get();

            foreach ($ShelfItems as $item) {
                $copy = \App\Models\Copy::findOrFail($item->copy_id);

                $copy->reservations()->create([
                    'borrower_id' => auth()->user()->id,
                ]);
            }

            \App\Models\ShelfItem::whereIn('id', $request->id)
                ->delete();

            $message .= 's have ';
        } else {
            $ShelfItem = \App\Models\ShelfItem::where('id', $request->id)->first();

            \App\Models\Copy::findOrFail($ShelfItem->copy_id)->reservations()->create([
                'borrower_id' => auth()->user()->id,
            ]);

            $message .= ' has ';
        }

        $message .= 'been successfully created!';
        return response()->json(['success' => $message]);
    }


    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update([
            'status' => 'canceled',
            'check_out_before' => null
        ]);
        $newReservation = $reservation->copy->reservations()
            ->where('id', '<>', $reservation->id)
            ->where('status', 'pending')
            ->oldest()
            ->first();

        if ($newReservation == null) {
            $reservation->copy()->update(['availability' => 'available']);
        } else {
            $newReservation->update([
                'check_out_before' => Carbon::now()->addDays(4)->startOfDay(),
                'status' => 'ready for check-out'
            ]);
        }

        return response()->json(['success' => 'Reservation successfully canceled!']);
    }

    public function destroy(Request $request)
    {
        $message = 'Reservation';
        $errorCount = 0;
        $successCount = 0;

        if (is_array($request->id)) {
            $reservations = Reservation::whereIn('id', $request->id)->get();

            foreach ($reservations as $reservation) {
                if ($this->deleteReservation($reservation) == 'error') {
                    $errorCount++;
                } else {
                    $successCount++;
                }
            }

            $message .= 's have ';
        } else {
            if ($this->deleteReservation(Reservation::findOrFail($request->id)) == 'error') {
                return response()->json(['error' => 'You cannot delete on-going reservation!']);
            }
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';

        if ($successCount == 0  && is_array($request->id)) {
            return response()->json(['error' => 'Reservations failed to be deleted!']);
        }

        return response()->json(['success' => $message . ($errorCount > 0 ? ' ' . $errorCount . ' failed.' : ' ')]);
    }

    private function deleteReservation(Reservation $reservation)
    {
        if ($reservation->status == 'ready for check-out') {
            return 'error';
        } else {
            $reservation->delete();
        }
    }

    public function restore(Request $request)
    {
        $message = 'Reservation';

        if (is_array($request->id)) {
            Reservation::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            Reservation::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Reservation';

        if (is_array($request->id)) {
            Reservation::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            Reservation::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }
}
