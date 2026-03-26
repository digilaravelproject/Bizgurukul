<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Google2FAService;

class TwoFactorChallengeController extends Controller
{
    protected $google2fa;

    public function __construct(Google2FAService $google2fa)
    {
        $this->google2fa = $google2fa;
    }
    /**
     * Show the 2FA challenge view.
     */
    public function create(Request $request)
    {
        if (! $request->session()->has('auth.2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Handle the 2FA challenge verification.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $userId = $request->session()->get('auth.2fa_user_id');
        $user = \App\Models\User::findOrFail($userId);

        $google2fa = $this->google2fa;
        $verified = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($verified) {
            Auth::login($user, $request->session()->get('auth.2fa_remember', false));
            
            $request->session()->forget(['auth.2fa_user_id', 'auth.2fa_remember']);
            $request->session()->put('auth.2fa_verified', true);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['code' => 'The provided code was incorrect.']);
    }
}
