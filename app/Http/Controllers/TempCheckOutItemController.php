<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TempCheckOutItem;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TempCheckOutItemController extends Controller
{
    private function button($icon, $id, $className, $disabled = false)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-temp-check-out-item-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '" ' . ($disabled == true ? 'disabled style="background: none !important;"' : '') . '><i class="' . $icon . '"></i></button>';
    }

    public function index()
    {
        $tempCheckOutItems = \App\Models\TempCheckOutItem::where('librarian_id', auth()->user()->id)
            ->with(['copy:id,barcode,availability,collection_id', 'copy.collection:id,title,format'])
            ->select('id', 'copy_id', 'due_at', 'grace_period_days');

        return Datatables::of($tempCheckOutItems)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';
                $html .= $this->button('fa-solid fa-xmark', $row->id, 'delete');
                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('barcode', function ($row) {
                return '<a href="' . route('collections.index') . '/' . $row->copy->collection_id . '">' . $row->copy->barcode . '</a>';
            })
            ->addColumn('title', function ($row) {
                return '<a href="' . route('collections.index') . '/' . $row->copy->collection_id . '">' . Str::limit($row->copy->collection->title, 25, '...') . '</a>';
            })
            ->addColumn('date_due_input', function ($row) {
                return '<div class="input-group"><div class="input-group-prepend"></div><input type="date" class="form-control date-due-input" data-id="' . $row->id . '" value="' . $row->due_at->format('Y-m-d') . '" min="' . Carbon::tomorrow()->format('Y-m-d') . '"></div>';
            })
            ->addColumn('availability', function ($row) {
                $statuses = [
                    'available' => 'success',
                    'on loan' => 'warning',
                    'reserved' => 'warning',
                    'lost' => 'danger'
                ];

                return '<span class="badge badge-' . $statuses[$row->copy->availability] . '">' . $row->copy->availability . '</span>';
            })
            ->rawColumns(['action', 'barcode', 'title', 'date_due_input', 'availability'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $copy = \App\Models\Copy::where('barcode', $request->barcode)->first();

        if(!$copy){
            return response()->json(['error' => 'Barcode does not exist!']);
        }

        $isAdded = TempCheckOutItem::where('copy_id', $copy->id)
            ->where('librarian_id', auth()->user()->id)
            ->exists();

        if ($isAdded) {
            return response()->json(['error' => 'Item already added!']);
        }

        $borrower = \App\Models\Patron::where('id2', $request->borrowerId)->first();

        if ($copy->availability == 'reserved') {
            $isReserved = $copy->reservations()
                ->where('borrower_id', $borrower->id)
                ->whereDate('check_out_before', '>=', now())
                ->exists();

            if (!$isReserved) {
                return response()->json(['error' => 'Item is reserved to another patron!']);
            }
        }

        if ($copy->availability == 'on loan') {
            return response()->json(['error' => 'Item is currently on loan!']);
        }

        if ($copy->availability == 'lost') {
            return response()->json(['error' => 'Item is lost!']);
        }

        // GET LOANING PERIOD
        $borrowerRoles = $borrower->getRoleNames();
        $prefix = $copy->call_prefix;
        $highestLoaningPeriodDays = 1;
        $highestGracePeriodDays = 0;

        if ($prefix) {
            $loaningPeriods = [];
            $gracePeriods = [];

            foreach ($borrowerRoles as $role) {
                $role = \Spatie\Permission\Models\Role::findByName($role);
                $holdingOption = \App\Models\HoldingOption::where('value', $prefix)->first();
                $loaningPeriod = \App\Models\LoaningPeriod::where('role_id', $role->id)
                    ->where('holding_option_id',  $holdingOption->id)
                    ->first();

                if ($loaningPeriod) {
                    $loaningPeriods[] = $loaningPeriod->no_of_days;
                    $gracePeriods[] = $loaningPeriod->grace_period_days;
                }
            }

            $highestLoaningPeriodDays = !empty($loaningPeriods) ? max($loaningPeriods) : 1;
            $highestGracePeriodDays = !empty($gracePeriods) ? max($gracePeriods) : 0;
        }

        if($highestLoaningPeriodDays == 0){
            return response()->json(['error' => 'This item is for room use only!']);
        }

        $copy->tempCheckOutItems()->create([
            'librarian_id' => auth()->user()->id,
            'due_at' => $this->addDaysExcludingSundays($highestLoaningPeriodDays)->setTime(8, 0, 0),
            'grace_period_days' => $highestGracePeriodDays
        ]);

        $totalOnLoanItems = $borrower->offSiteCirculations()->whereNull('checked_in_at')->count() + auth()->user()->tempCheckOutItems()->count();

        if ($totalOnLoanItems > 5 && $borrower->hasRole('faculty')) {
            return response()->json(['info' => 'Maximum borrowed books exceeded!']);
        }

        if ($totalOnLoanItems > 3 && !$borrower->hasRole('faculty')) {
            return response()->json(['info' => 'Maximum borrowed books exceeded!']);
        }
    }

    public function changeDateDue(Request $request, $id)
    {
        $tempCheckOutItem = TempCheckOutItem::find($id);
        $tempCheckOutItem->due_at = Carbon::createFromFormat('Y-m-d', $request->due_at)->setTime(8, 0, 0);
        $tempCheckOutItem->save();
    }

    public function destroy($id)
    {
        TempCheckOutItem::find($id)->delete();
    }

    public function removeAll()
    {
        TempCheckOutItem::where('librarian_id', auth()->user()->id)->delete();

        return response()->json(['success' => 'Check-out items have been successfully removed!']);
    }

    private function addDaysExcludingSundays($numberOfDaysToAdd)
    {
        $currentDate = Carbon::today();

        for ($i = 0; $i < $numberOfDaysToAdd; $i++) {
            $currentDate->addDay();

            if ($currentDate->dayOfWeek === Carbon::SUNDAY) {
                $currentDate->addDay();
            }
        }

        return $currentDate;
    }
}
