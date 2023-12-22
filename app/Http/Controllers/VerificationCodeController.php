<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VerificationCode;
use App\Models\Registration;
use Carbon\Carbon;
use App\Models\Patron;
use Illuminate\Support\Facades\Mail;
use App\Mail\Registration as RegistrationMail;
use App\Mail\ForgotPassword as ForgotPasswordMail;


class VerificationCodeController extends Controller
{
    public function index($token)
    {
        $code = \App\Models\VerificationCode::where('token', $token)->first();

        return view('auth.otp-verification')->with([
            'token' => $token,
            'title' => $code->type == 'verify_email' ? 'Verify Email' : 'Forgot Password',
            'email' => $code->email
        ]);
    }

    public function verify(Request $request)
    {
        $code = VerificationCode::where('token', $request->token)
            ->where('is_activated', false)
            ->first();

        $fiveMinutesAgo = Carbon::now()->subMinutes(5);
        $otpCreatedAt = Carbon::parse($code->created_at);

        if ($request->otp != $code->code) {
            session()->flash('message', 'Incorrect OTP code!');
            return back();
        }

        // GREATER THAN OR EQUAL
        if ($otpCreatedAt->gte($fiveMinutesAgo)) {
            if ($code->type == 'verify_email') {
                $patron = Patron::where('email', $code->email)->first();

                $patron->update([
                    'email_verified_at' => Carbon::now()
                ]);

                $code->delete();

                return redirect()->route('login')->with(['message' => 'Email successfully verified! Your account is pending librarian approval.']);
            }

            if ($code->type == 'forgot_password') {
                $code->update([
                    'is_activated' => true
                ]);

                return redirect()->route('forgot.password.index', [
                    'token' => $request->token
                ]);
            }
        } else {
            session()->flash('message', 'OTP code has expired!');
            return back();
        }
    }

    public function resend(Request $request)
    {
        $oldVerificationCode = \App\Models\VerificationCode::where('token', $request->token)->first();
        $newCode = random_int(100000, 999999);

        $newVerificationCode = VerificationCode::create([
            'code' => $newCode,
            'email' => $oldVerificationCode->email,
            'type' => $oldVerificationCode->type
        ]);

        $oldVerificationCode->delete();

        if ($newVerificationCode->type == 'verify_email') {
            Mail::to($newVerificationCode->email)->send(new RegistrationMail($newCode));

            return redirect()->route('otp.verification.index', [
                'token' => $newVerificationCode->token,
            ]);
        } else if ($newVerificationCode->type == 'forgot_password') {
            Mail::to($newVerificationCode->email)->send(new ForgotPasswordMail($newCode));

            return redirect()->route('otp.verification.index', [
                'token' => $newVerificationCode->token,
            ]);
        } else {
            return back();
        }
    }
}
