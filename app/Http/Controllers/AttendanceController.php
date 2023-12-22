<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Route;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
use Avatar;

class AttendanceController extends Controller
{
    private function button($icon, $id, $className)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg btn-attendance-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '"> <i class="' . $icon . '"></i> </button>';
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('attendance.index');
        }

        $attendance = Attendance::with(['librarian:id,first_name,last_name', 'patron:id,id2,first_name,last_name', 'patron.images:id,file_name,imageable_type,imageable_id'])
            ->select()
            ->orderBy('created_at', 'desc');

        if (auth()->user()->temp_role != 'librarian') {
            $attendance = $attendance->where('patron_id', auth()->user()->id);
        }

        if (Route::currentRouteName() == 'attendance.archive') {
            $attendance = $attendance->onlyTrashed();
        }

        return Datatables::of($attendance)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                if (Route::currentRouteName() == 'attendance.index') {
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'delete');
                } else if (Route::currentRouteName() == 'attendance.archive') {
                    $html .= $this->button('fa fa-undo', $row->id, 'restore');
                    $html .= $this->button('fa-solid fa-trash-can', $row->id, 'force-delete');
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('roles', function ($row) {
                $roles = $row->patron->getRoleNames()->map(function ($role) {
                    return Str::title($role);
                })->implode(', ');

                return $roles;
            })
            ->addColumn('full_name', function ($row) {
                return '<a href="' . route("patrons.index") . '/' . $row->patron_id . '">' . $row->patron->last_name . ', ' . $row->patron->first_name . '</a>';
            })
            ->addColumn('time_in', function ($row) {
                return $row->created_at->format('Y-m-d g:i A');
            })
            ->addColumn('id2', function ($row) {
                return $row->patron->id2;
            })
            ->addColumn('image', function ($row) {
                $image = $row->patron->images()->first();

                if ($image != null) {
                    return '<div class = "avatar avatar-sm" ><img src = "' . asset('storage/images/patrons/' . $image->file_name) . '" class = "avatar-img rounded-circle" ></div>';
                } else {
                    return '<div class = "avatar avatar-sm" ><img src = "' . (Avatar::create($row->patron->first_name . ', ' . $row->patron->last_name)->setFontFamily('Lato')->toBase64()) . '" class = "avatar-img rounded-circle" ></div>';
                }
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->rawColumns(['action', 'image', 'checkbox', 'groups', 'full_name'])
            ->make(true);
    }

    public function store($id)
    {
        $patron = \App\Models\Patron::withTrashed()->where('id2', $id)->first();

        if (!$patron) {
            return response()->json(['error' => 'Patron not found!']);
        }

        if ($patron->deleted_at) {
            return response()->json(['error' => 'Patron is currently archived!']);
        }

        $image = $patron->images()->latest()->first();
        $image = $image != null ? asset('storage/images/patrons/' . ($image->file_name)) : Avatar::create($patron->first_name . ', ' . $patron->last_name)->setFontFamily('Lato')->toBase64();
        $roles = $patron->getRoleNames()->map(function ($role) {
            return Str::title($role);
        })->implode(', ');

        $attendance = Attendance::create([
            'librarian_id' => auth()->user()->id,
            'patron_id' => $patron->id
        ]);

        return response()->json([
            'patron' => $patron,
            'image' => $image,
            'roles' => $roles,
            'created_at' => $attendance->created_at->format('Y-m-d g:i A')
        ]);
    }

    public function destroy(Request $request)
    {
        $message = 'Attendance record';

        if (is_array($request->id)) {
            Attendance::whereIn('id', $request->id)->get()->each->delete();
            $message .= 's have ';
        } else {
            Attendance::findOrFail($request->id)->delete();
            $message .= ' has ';
        }

        $message .= 'been successfully deleted!';
        return response()->json(['success' => $message]);
    }

    public function restore(Request $request)
    {
        $message = 'Attendance record';

        if (is_array($request->id)) {
            Attendance::onlyTrashed()->whereIn('id', $request->id)->get()->each->restore();
            $message .= 's have ';
        } else {
            Attendance::onlyTrashed()->findOrFail($request->id)->restore();
            $message .= ' has ';
        }

        $message .= 'been successfully restored!';
        return response()->json(['success' => $message]);
    }

    public function forceDelete(Request $request)
    {
        $message = 'Attendance record';

        if (is_array($request->id)) {
            Attendance::onlyTrashed()->whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            Attendance::onlyTrashed()->findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully force deleted!';
        return response()->json(['success' => $message]);
    }
}
