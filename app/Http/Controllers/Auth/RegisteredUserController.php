<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use App\Models\AffiliateCommission;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        $states = DB::table('states')->orderBy('name')->get();
        return view('auth.register', compact('states'));
    }

    /**
     * AJAX Check: Referral code live validation
     */
    public function checkReferral(Request $request)
    {
        try {
            $code = $request->input('code');
            if (!$code)
                return response()->json(['status' => 'empty']);

            $referrer = User::where('referral_code', $code)->first();

            if ($referrer) {
                return response()->json([
                    'status' => 'valid',
                    'message' => 'Referral Applied: ' . $referrer->name
                ]);
            }

            return response()->json(['status' => 'invalid', 'message' => 'Invalid Referral Code.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Something went wrong.']);
        }
    }

    /**
     * Store: User registration with Error Handling
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validation
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'mobile' => ['nullable', 'numeric', 'digits_between:10,15'],
            'gender' => ['nullable', 'in:male,female,other'],
            'dob' => ['nullable', 'date'],
            'state_id' => ['nullable', 'exists:states,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code'],
        ]);

        // 2. Execution with Try-Catch & Transaction
        DB::beginTransaction();
        try {
            // Determine Referrer logic
            $referredBy = null;
            $referralCode = $request->referral_code ?? Cookie::get('referral_code');

            if (Setting::get('referral_system_active') === '1' && $referralCode) {
                $referrer = User::where('referral_code', $referralCode)->first();
                $referredBy = $referrer?->id;
            }

            // Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'state_id' => $request->state_id,
                'password' => Hash::make($request->password),
                'is_active' => true,
                'referred_by' => $referredBy,
            ]);

            // Assign Student Role
            if ($user) {
                $user->assignRole('Student');
            }

            // Commission Logic (If applicable at registration)
            $commAmount = (float) Setting::get('referral_commission_amount', 0);
            if ($referredBy && $commAmount > 0) {
                app(\App\Services\WalletService::class)->processCommission([
                    'affiliate_id' => $referredBy,
                    'referred_user_id' => $user->id,
                    'amount' => $commAmount,
                    'notes' => 'Registration bonus commission.',
                ]);

                // Referral code consume ho gaya, cookie queue for removal
                Cookie::queue(Cookie::forget('referral_code'));
            }

            DB::commit();

            event(new Registered($user));
            Auth::login($user);

            // Redirect to Product Selection instead of Onboarding
            return redirect()->route('student.product_selection');

        } catch (\Exception $e) {
            DB::rollBack();

            // Error ko log file mein save karna (storage/logs/laravel.log)
            Log::error("Registration Error: " . $e->getMessage(), [
                'email' => $request->email,
                'referral_code' => $request->referral_code
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Something went wrong during registration. Please try again.']);
        }
    }
}
