<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

            // Akun untuk email ini HARUS sudah dibuatkan oleh admin lebih dulu --
            // Login Google TIDAK boleh membuat akun baru secara otomatis.
            $user = User::where('email', $googleUser->getEmail())->first();

            if (! $user) {
                return redirect()->route('login')->with(
                    'error',
                    'Your email has not been registered by an admin yet. Please contact an admin to create an account for you first.'
                );
            }

            // Login Google pertama kali untuk akun ini (baru dibuatkan admin,
            // belum pernah dihubungkan ke Google sebelumnya) -- hubungkan google_id-nya.
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        }

        Auth::login($user, true);

        $user->update([
            'last_login_at' => now(),
            'is_online' => true
        ]);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
