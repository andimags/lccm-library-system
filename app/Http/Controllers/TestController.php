<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Mail\DueDateNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Jimmyjs\ReportGenerator\Facades\PdfReportFacade;
use Jimmyjs\ReportGenerator\Facades\ExcelReportFacade;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

class TestController extends Controller
{
    public function test(Request $request)
    {
        // CHECK IF ENABLE AUTOMATIC FINES IS SET TO 'YES'
        $enableAutomaticFines = \App\Models\Setting::where('field', 'enable_automatic_fines')->first();

        if ($enableAutomaticFines->value == 'no') {
            return;
        }

        $overdueCirculations = \App\Models\OffSiteCirculation::where('status', 'checked-out')
            ->where('due_at', '<=', Carbon::today()->setTime(8, 0, 0));

        $currentTime = Carbon::now()->format('H');
        $targetTime = Carbon::now()->setTime(8, 0)->format('H');

        if ($currentTime === $targetTime) {
            // Current time is 8:00 AM (Fine both important & unimportant copies)
            $overdueCirculations = $overdueCirculations->get();

            foreach ($overdueCirculations as $circulation) {
                // Calculate due date with grace period
                $dueDateWithGrace = Carbon::parse($circulation->due_at)
                    ->addDays($circulation->grace_period_days);

                if ($dueDateWithGrace->lt(Carbon::now())) {
                    $circulation->fines()->create([
                        'reason' => 'Overdue Penalty',
                        'price' => 5.00
                    ]);

                    $circulation->total_fines = $circulation->fines()->sum('price');
                    $circulation->save();
                }
            }
        } else {
            // Current time is not 8:00 AM (Fine important copies only)
            $importantCirculations = $overdueCirculations
                ->whereHas('copy', function ($query) {
                    $query->where('call_prefix', 'important');
                })
                ->get();


            foreach ($importantCirculations as $circulation) { {
                    // Calculate due date with grace period
                    $dueDateWithGrace = Carbon::parse($circulation->due_at)
                        ->addDays($circulation->grace_period_days);

                    // Check if the due date (plus grace period) is after today
                    if ($dueDateWithGrace->lt(Carbon::now())) {
                        $circulation->fines()->create([
                            'reason' => 'Overdue Penalty',
                            'price' => 5.00
                        ]);

                        $circulation->total_fines = $circulation->fines()->sum('price');
                        $circulation->save();
                    }
                }
            }
        }
    }

    public function insertHolidays()
    {
        $client = new Client();
        $response = $client->get('https://calendarific.com/api/v2/holidays?api_key=' . env('CALENDARIFIC_API_KEY') . '&country=PH&year=2023');
        $response = json_decode($response->getBody(), true);
        $holidays = $response['response']['holidays'];

        foreach ($holidays as $holiday) {
            $date = Carbon::parse($holiday['date']['iso']);
            $formattedDate = $date->format('m-d');

            \App\Models\Holiday::create([
                'name' => $holiday['name'],
                'date' => $formattedDate
            ]);
        }
    }

    public function addWorkingDays($date)
    {
        $today = Carbon::parse($date);

        $workingDays = 0;
        $counter = 0;
        while ($workingDays < 3) {
            $counter++;
            $nextDay = $today->copy()->addDays($counter);
            if (!$nextDay->isWeekend() && !\App\Models\Holiday::where('date', $nextDay->format('m-d'))->exists()) {
                $workingDays++;
            }
        }

        $threeWorkingDaysFromToday = $today->copy()->addDays($counter);
        return $threeWorkingDaysFromToday->format('Y-m-d');
    }

    public function pdf()
    {
        $title = 'Registered User Report'; // Report title

        $meta = [
            'sample' => 'sample'
        ];

        $queryBuilder = \App\Models\Patron::select(); // Do some querying..

        $columns = [ // Set Column to be displayed
            'first name',
            'last name',
            'email'
        ];

        return PdfReportFacade::of($title, $meta, $queryBuilder, $columns)
            ->limit(20)
            ->stream();
    }

    public function excel()
    {
        $title = 'Registered User Report'; // Report title

        $meta = [
            'sample' => 'sample'
        ];

        $queryBuilder = \App\Models\Patron::select(); // Do some querying..

        $columns = [ // Set Column to be displayed
            'first name',
            'last name',
            'email'
        ];

        return ExcelReportFacade::of($title, $meta, $queryBuilder, $columns)
            ->simple()
            ->download('filename');
    }
}
