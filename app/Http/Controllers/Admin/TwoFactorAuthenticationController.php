<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Google2FAService;

class TwoFactorAuthenticationController extends Controller
{
    protected $google2fa;

    public function __construct(Google2FAService $google2fa)
    {
        $this->google2fa = $google2fa;
    }
    /**
     * Show the 2FA settings page.
     */
    public function index()
    {
        $user = Auth::user();
        $google2fa = $this->google2fa;

        $qrCodeSvg = null;
        $secret = null;

        if (!$user->two_factor_confirmed_at) {
            // Generate secret if not exists or not confirmed
            if (!$user->two_factor_secret) {
                $user->two_factor_secret = $google2fa->generateSecretKey();
                $user->save();
            }

            $secret = $user->two_factor_secret;
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );

            // Use public API for QR Code instead of local dependency to avoid installation issues
            $qrCodeSvg = '<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl) . '" alt="QR Code" class="mx-auto rounded-xl shadow-lg border-4 border-white">';
        }

        return view('admin.settings.2fa', compact('user', 'qrCodeSvg', 'secret'));
    }

    /**
     * Enable 2FA after verifying a code.
     */
    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = Auth::user();
        $google2fa = $this->google2fa;

        $verified = $google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($verified) {
            $user->two_factor_confirmed_at = now();
            $user->save();

            return back()->with('success', 'Two-Factor Authentication has been enabled.');
        }

        return back()->withErrors(['code' => 'The provided code was incorrect.']);
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return back()->with('success', 'Two-Factor Authentication has been disabled.');
    }
}
