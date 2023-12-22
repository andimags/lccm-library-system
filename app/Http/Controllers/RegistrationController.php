<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patron;
use Illuminate\Support\Facades\Mail;
use App\Mail\Registration as RegistrationMail;
use App\Models\VerificationCode;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;


class RegistrationController extends Controller
{
    private function rules(Request $request)
    {
        $rules = [
            'id2' => ['required', 'numeric'],
            'first_name' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'min:2', 'max:30'],
            'last_name' => ['required', 'string', 'regex:/^[\pL\s]+$/u', 'min:2', 'max:20'],
            'email' => ['required', 'email', Rule::unique('patrons')],
            'roles' => [new \App\Rules\MultivaluedMax(2)],
            'password' => ['required', new \App\Rules\Password()],
            'confirm_password' => ['same:password'],
        ];

        $validator = Validator::make($request->all(), $rules);
        $validator->setAttributeNames(['id2' => 'id']); // Replace 'id2' with 'id' in the attribute names

        return $validator;
    }

    private function button($value, $id, $className)
    {
        return '<button type="button" data-toggle="tooltip" title="" class="btn btn-link btn-primary btn-registration-' . $className . '" data-original-title="Edit Task" data-id="' . $id . '">' . $value . '</button>';
    }

    public function index(Request $request)
    {
        if (!$request->ajax()) {
            return view('registrations.index');
        }

        $registrations = Patron::with(['groups:id,group', 'roles'])
            ->select('id', 'id2', 'first_name', 'last_name')
            ->where('id', '<>', auth()->user()->id)
            ->where('registration_status', 'pending')
            ->whereNotNull('email_verified_at')
            ->orderBy('created_at', 'desc');

        if (Route::currentRouteName() == 'registrations.archive') {
            $registrations = $registrations->onlyTrashed();
        }

        return Datatables::of($registrations)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $html = '<td> <div class="form-button-action">';

                if (Route::currentRouteName() == 'registrations.index') {
                    $html .= $this->button('Accept', $row->id, 'accept',);
                    $html .= $this->button('Decline', $row->id, 'decline',);
                } else if (Route::currentRouteName() == 'registrations.archive') {
                    $html .= $this->button('<i class="fa fa-undo"></i>', $row->id, 'restore');
                    $html .= $this->button('<i class="fa-solid fa-trash-can"></i>', $row->id, 'force-delete');
                }

                $html .= '</div> </td>';

                return $html;
            })
            ->addColumn('roles', function ($row) {
                $roles = $row->roles->pluck('name')->map(function ($role) {
                    return Str::title($role);
                })->implode(', ');

                return $roles;
            })
            ->filterColumn('roles', function ($query, $keyword) {
                $query->whereHas('roles', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="select-checkbox" data-checkboxes="true" name="ids[]" value="' . $row->id . '">';
            })
            ->rawColumns(['action', 'checkbox'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $patron = Patron::create([
            'id2' => $request->id2,
            'first_name' => Str::title($request->first_name),
            'last_name' => Str::title($request->last_name),
            'email' => strtolower($request->email),
            'role' => $request->role,
            'registration_status' => 'pending',
            'password' => Hash::make($request->password)
        ]);

        $patron->syncRoles([$request->role]);

        $code = random_int(100000, 999999);

        $verificationCode = VerificationCode::create([
            'code' => $code,
            'email' => $request->email,
            'type' => 'verify_email'
        ]);

        Mail::to($request->email)->send(new RegistrationMail($code));

        return response()->json([
            'redirect' => route('otp.verification.index', ['token' => $verificationCode->token])
        ]);
    }

    public function accept(Request $request)
    {
        $message = 'Registration';

        if (!is_array($request->id)) {
            $this->acceptRegistration($request->id);
            $message .= ' has ';
        } else {
            foreach ($request->id as $id) {
                $this->acceptRegistration($id);
            }
            $message .= 's have ';
        }

        $message .= 'been successfully accepted!';
        return response()->json(['success' => $message]);
    }

    public function decline(Request $request)
    {
        $message = 'Registration';

        if (is_array($request->id)) {
            Patron::whereIn('id', $request->id)->get()->each->forceDelete();
            $message .= 's have ';
        } else {
            Patron::findOrFail($request->id)->forceDelete();
            $message .= ' has ';
        }

        $message .= 'been successfully declined!';
        return response()->json(['success' => $message]);
    }

    private function acceptRegistration($id)
    {
        $librarianId = auth()->user()->id;
        $patron = Patron::find($id);

        $patron->update([
            'librarian_id' => $librarianId,
            'registration_status' => 'accepted'
        ]);
    }
}
