<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;
use App\Models\Renewal;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RenewalController extends Controller
{
    private function button($display, $id, $className, $size = 'lg', $disabled = false)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-' . $size . ' btn-renewal-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '" ' . ($disabled == true ? 'disabled style="background: none !important;"' : '') . '>' . $display . '</button>';
    }

    public function index($circulation_id)
    {
        $renewals = \App\Models\OffSiteCirculation::withTrashed()
            ->find($circulation_id)
            ->renewals()
            ->with('librarian:id,first_name,last_name')
            ->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'renewals.archive') {
            $renewals = $renewals->onlyTrashed();
        }

        return Datatables::of($renewals)
            ->addIndexColumn()
            ->addColumn('old_due_at', function ($row) {
                return $row->old_due_at->format('Y-m-d g:i A');
            })
            ->filterColumn('old_due_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(old_due_at, '%Y-%m-%d') like ?", ["%$keyword%"]);
            })
            ->addColumn('new_due_at', function ($row) {
                return $row->new_due_at->format('Y-m-d g:i A');
            })
            ->filterColumn('new_due_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(new_due_at, '%Y-%m-%d') like ?", ["%$keyword%"]);
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at->format('Y-m-d g:i A');
            })
            ->filterColumn('created_at', function ($query, $keyword) {
                $query->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d %h:%i %p') like ?", ["%$keyword%"]);
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
            ->rawColumns(['librarian'])
            ->make(true);
    }
}
