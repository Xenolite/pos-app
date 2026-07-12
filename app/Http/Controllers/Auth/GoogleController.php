<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::error('Google login failed: '.$e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Login with Google failed. Please try again.');
        }


        $user = User::where('google_id', $googleUser->getId())->first();

        if (! $user) {

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            } else {

                $user = User::create([
                    'name' => $googleUser->getName() ?? $googleUser->getNickname(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(Str::random(24)),
                    'email_verified_at' => now(), 
                ]);
            }
        }

        Auth::login($user, true);

        $user->update([
            'last_login_at' => now(),
            'is_online' => true
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
