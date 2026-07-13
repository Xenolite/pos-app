<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
public function index()
    {
        return view('user-profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'nullable'
        ]);

        $user = auth()->user();

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone
        ]);

        return back()->with('success', 'Profile updated!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = auth()->user();

        

        if(!Hash::check($request->current_password, $user->password)){
            return back()->with('error', 'Current password incorrect');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed!');
    }

    public function toggleDarkMode()
{
    $user = auth()->user();

    $user->dark_mode = ! $user->dark_mode;
    $user->save();

    return back();
}
}