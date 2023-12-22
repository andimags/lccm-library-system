<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {

        $patron = \App\Models\Patron::withTrashed()->where('id2', $request->id)->first();

        if (!$patron) {
            session()->flash('message', 'Invalid email/password!');
            return back();
        } else if ($patron->deleted_at) {
            session()->flash('message', 'Your account is currently archived!');
            return back();
        }

        Auth::loginUsingId($patron->id);

        if (count(auth()->user()->getRoleNames()) > 1) {
            return redirect()->route('login.as');
        } else {
            $patron->update([
                'temp_role' => auth()->user()->getRoleNames()[0]
            ]);
        }

        return redirect()->route('dashboard.index');
    }

    public function logout()
    {
        auth()->logout();
        return redirect()->route('login');
    }

    public function editProfile()
    {
        $patron = auth()->user();
        $role = $patron->getRoleNames()->first();
        $image = $patron->images()->latest()->first();

        if ($image != null) {
            $image = '<div class="avatar avatar-xxl mt-3"><img src="' . asset('images/patrons') . '/' . $image->file_name . '" alt="..." class="avatar-img rounded"></div>';
        }

        return view('patrons.edit-profile')->with([
            'patron' => $patron,
            'role' => $role,
            'image' => $image
        ]);
    }

    public function profile()
    {
        $image = auth()->user()?->images->first()?->file_name ?? 'default.jpg';
        $patron = auth()->user();

        return view('patrons.show', [
            'image' => $image,
            'patron' => $patron
        ]);
    }

    public function loginAs()
    {
        return view('auth.login-as')
            ->with('roles', auth()->user()->getRoleNames());
    }

    public function selectTempRole(Request $request)
    {
        $patron = auth()->user(); // Get the authenticated user
        $patron->temp_role = $request->temp_role; // Update the 'temp_role' attribute
        $patron->save(); // Save changes to the user model

        return redirect()->route('dashboard.index');
    }

    public function changePassword(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'old_password' => ['required', function ($attribute, $value, $fail) {
                $patron = auth()->user();

                if (!$patron || !Hash::check($value, $patron->password)) {
                    $fail('The old password is incorrect.');
                }
            }],
            'new_password' => ['required'],
            'confirm_new_password' => ['required', 'same:new_password'],
        ]);

        if ($validated->fails()) {
            return response()->json(['code' => 400, 'msg' => $validated->errors()]);
        }

        $patron = auth()->user();
        $patron->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['success' => 'Password changed successfully!']);
    }

    public function toggleDisplayMode(Request $request)
    {
        auth()->user()->update([
            'display_mode' => $request->display_mode
        ]);

        return back();
    }
}
