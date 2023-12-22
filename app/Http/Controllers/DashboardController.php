<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patron;
use Carbon\Carbon;
use App\Models\Collection;

class DashboardController extends Controller
{
    public function index()
    {
        $announcements = \App\Models\Announcement::with(['images'])
            ->where('visibility', 'all')
            ->orWhere('visibility', auth()->user()->temp_role)
            ->whereDate('start_at', '<=', Carbon::now()->toDateString()) // Start date is less than or equal to today
            ->whereDate('end_at', '>=', Carbon::now()->toDateString())   // End date is greater than or equal to today
            ->latest()
            ->get();

        $totalCirculations = null;
        $totalUnpaidFines = null;
        $totalOnLoans = null;
        $totalReservations = null;

        $view = view('dashboard.index')
            ->with('announcements', $announcements);

        if (auth()->user()->temp_role == 'librarian') {
            $librarians = Patron::role('librarian')->count();
            $employees = Patron::role('employee')->count();
            $faculties = Patron::role('faculty')->count();
            $students = Patron::role('student')->count();

            $totalCirculations = \App\Models\OffSiteCirculation::count();
            $totalUnpaidFines =  \App\Models\OffSiteCirculation::where('fines_status', 'unpaid')
                ->where('total_fines', '>', 0)
                ->sum('total_fines');
            $totalOnLoans = \App\Models\OffSiteCirculation::where('status', 'checked-out')->count();
            $totalReservations = \App\Models\Reservation::count();

            $view = $view
                ->with('librarians', number_format($librarians, 0, '.', ','))
                ->with('employees', number_format($employees, 0, '.', ','))
                ->with('faculties', number_format($faculties, 0, '.', ','))
                ->with('students', number_format($students, 0, '.', ','))
                ->with('usageStatistics', $this->usageStatistics());
        } else {
            // IF BORROWER
            $totalCirculations = \App\Models\OffSiteCirculation::where('borrower_id', auth()->user()->id)->count();
            $totalUnpaidFines =  \App\Models\OffSiteCirculation::where('borrower_id', auth()->user()->id)->where('fines_status', 'unpaid')
                ->where('total_fines', '>', 0)
                ->sum('total_fines');
            $totalOnLoans = \App\Models\OffSiteCirculation::where('borrower_id', auth()->user()->id)->where('status', 'checked-out')->count();
            $totalReservations = \App\Models\Reservation::where('borrower_id', auth()->user()->id)->count();

            $circulationsDueTomorrow = null;
            $circulationsOverdue = null;
            $messageTitle = '';
            $messageText = '';

            $circulationsDueTomorrow = auth()->user()
                ->offSiteCirculations()
                ->whereNull('checked_in_at')
                ->whereDate('due_at', now()->addDay()->toDateString())
                ->count();

            $circulationsOverdue = auth()->user()
                ->offSiteCirculations()
                ->whereNull('checked_in_at')
                ->whereDate('due_at', '<', now()) // Check if due_at is in the past
                ->count();

            $messageTitle = ($circulationsDueTomorrow ? $circulationsDueTomorrow . ' Circulation(s) Due Tomorrow' : '') .
                ($circulationsDueTomorrow && $circulationsOverdue ? '<br>' : '') .
                ($circulationsOverdue ? $circulationsOverdue . ' Circulation(s) Overdue' : '');

            $messageText = ($circulationsDueTomorrow ? 'You have ' . $circulationsDueTomorrow . ' item(s) due tomorrow. Please remember to return or renew them in time.' : '') . ($circulationsDueTomorrow && $circulationsOverdue ? '<br><br>' : '') . ($circulationsOverdue ? 'You have ' . $circulationsOverdue . ' item(s) overdue. Please remember to check-in or mark any overdue circulations as lost to avoid overdue penalty.' : '');


            $newCollections = Collection::latest()
                ->with(['authors:author', 'images:file_name'])
                ->limit(5)
                ->get();

            $view = $view->with('newCollections', $newCollections)
                ->with('messageTitle', $messageTitle)
                ->with('messageText', $messageText);
        }

        return $view->with('totalCirculations', number_format($totalCirculations, 0, '.', ','))
            ->with('totalUnpaidFines', number_format($totalUnpaidFines, 2, '.', ','))
            ->with('totalOnLoans', number_format($totalOnLoans, 0, '.', ','))
            ->with('totalReservations', number_format($totalReservations, 0, '.', ','));
    }

    private function usageStatistics()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $last12Months = array();

        for ($i = 0; $i < 12; $i++) {
            $last12Months[] = date('F Y', strtotime("-$i months", strtotime("$currentYear-$currentMonth-01")));
        }

        $usageStatistics = array();

        foreach ($last12Months as $monthAndYear) {
            $month = explode(' ', $monthAndYear)[0];
            $year = explode(' ', $monthAndYear)[1];

            $renewalsCount = $this->getModelCount(\App\Models\Renewal::class, $month, $year);
            $reservationsCount = $this->getModelCount(\App\Models\Reservation::class, $month, $year);
            $offSiteCount = $this->getModelCount(\App\Models\OffSiteCirculation::class, $month, $year);
            $inHouseCount = $this->getModelCount(\App\Models\InHouseCirculation::class, $month, $year);
            $total = $renewalsCount + $reservationsCount + $offSiteCount + $inHouseCount;

            $usageStatistics[$monthAndYear] = [
                number_format($renewalsCount, 0, '.', ','), 
                number_format($reservationsCount, 0, '.', ','), 
                number_format($offSiteCount, 0, '.', ','), 
                number_format($inHouseCount, 0, '.', ','), 
                number_format($total, 0, '.', ',')
            ];
        }

        return $usageStatistics;
    }

    private function getModelCount($model, $month, $year)
    {
        return $model::whereYear('created_at', $year)
            ->whereMonth('created_at', date("m", strtotime($month)))
            ->count();
    }
}
