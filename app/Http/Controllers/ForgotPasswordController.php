<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patron;
use App\Models\VerificationCode;
use App\Mail\ForgotPassword as ForgotPasswordMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Rules\EmailExists;

class ForgotPasswordController extends Controller
{
    private function rules(Request $request)
    {
        $rules = [
            'email' => ['required', 'email', new EmailExists],
        ];

        $validator = Validator::make($request->all(), $rules);

        return $validator;
    }

    public function index($token)
    {
        $code = VerificationCode::where('token', $token)
            ->where('is_activated', true)
            ->first();

        if (!$code) {
            return back();
        }

        return view('auth.change-password')->with([
            'token' => $token
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->rules($request);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $code = random_int(100000, 999999);

        $verificationCode = VerificationCode::create([
            'code' => $code,
            'email' => $request->email,
            'type' => 'forgot_password'
        ]);

        Mail::to($request->email)->send(new ForgotPasswordMail($code));

        return response()->json([
            'redirect' => route('otp.verification.index', [
                'token' => $verificationCode->token,
            ])
        ]);
    }

    public function changePassword(Request $request)
    {
        if ($request->new_password == $request->confirm_password) {
            $code = VerificationCode::where('token', $request->token)
                ->where('is_activated', true)
                ->first();

            if (!$code) {
                return back();
            }

            $code->delete();

            $patron = \App\Models\Patron::where('email', $code->email)->first();

            $patron->update([
                'password' => Hash::make($request->new_password)
            ]);

            Auth::loginUsingId($patron->id);

            if (count(auth()->user()->getRoleNames()) > 1) {
                return redirect()->route('login.as')->with('message', 'Password changed successfully!');
            } else {
                $patron->update([
                    'temp_role' => auth()->user()->getRoleNames()[0]
                ]);
            }

            return redirect()->route('dashboard.index');
        } else {
            return dd('password do not match');
            return view('auth.change-password')->with(['message' => 'Passwords do not match']);
        }
    }
}
