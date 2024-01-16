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
        $client = new \GuzzleHttp\Client();

        $response = $client->request('POST', 'https://api.paymongo.com/v1/sources', [
            'body' => '{"data":{"attributes":{"amount":10000,"redirect":{"success":"http://127.0.0.1:8000/success","failed":"http://127.0.0.1:8000/failed"},"type":"gcash","currency":"PHP"}}}',
            'headers' => [
                'accept' => 'application/json',
                'authorization' => 'Basic c2tfdGVzdF9YSHpjQXQ2SGU4WlNlTmlxeDNTUnRzbUg6',
                'content-type' => 'application/json',
            ],
        ]);

        $response = json_decode($response->getBody(), true);
        $id = $response['data']['id'];

        return dd($id);
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
