<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Lead;
use App\Models\State;
use App\Models\User;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ManualOnboardingController extends Controller
{
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * Show the manual onboarding form.
     */
    public function index()
    {
        $bundles = Bundle::where('is_active', true)->ordered()->get();
        $states = State::orderBy('name')->get();

        return view('admin.manual-onboarding', compact('bundles', 'states'));
    }

    /**
     * Store the manual onboarding data and sync with production registration flow.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'mobile'         => 'required|numeric|digits:10',
            'password'       => 'required|min:8',
            'gender'         => 'required|in:male,female,other',
            'state_id'       => 'required|exists:states,id',
            'bundle_id'      => 'required|exists:bundles,id',
            'referral_code'  => 'nullable|string|exists:users,referral_code',
            'transaction_id' => 'required|string|unique:payments,gateway_payment_id',
            'amount'         => 'required|numeric|min:0',
            'payment_date'   => 'required|date',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Create a temporary Lead to bridge into RegistrationService
                // RegistrationService expects a Lead to exist and uses its attributes.
                $lead = Lead::create([
                    'name'               => $request->name,
                    'email'              => $request->email,
                    'mobile'             => $request->mobile,
                    'password'           => Hash::make($request->password),
                    'gender'             => $request->gender,
                    'state_id'           => $request->state_id,
                    'product_preference' => ['bundle_id' => $request->bundle_id],
                    'referral_code'      => $request->referral_code,
                    'ip_address'         => $request->ip(),
                ]);

                // 2. Prepare mock data to satisfy RegistrationService requirement
                $verifyData = [
                    'gateway'             => 'manual',
                    'is_webhook'          => true, // Skips external gateway verification
                    'lead_id'             => $lead->id,
                    'gateway_order_id'    => 'MANUAL_ORD_' . time(),
                    'gateway_payment_id'  => $request->transaction_id,
                    'coupon_code'         => null,
                ];

                // 3. Execute the EXACT production registration sequence
                // This handles: User Creation, Role Assignment, Pricing, Payment Recording,
                // Affiliate Commission, Lead Cleanup, and Email Notifications.
                $user = $this->registrationService->verifyAndCompleteRegistration($verifyData);

                // 4. Force override of manual payment details if they differ from calculated pricing
                $payment = $user->payments()->latest()->first();
                if ($payment) {
                    $payment->update([
                        'amount'       => $request->amount,
                        'total_amount' => $request->amount,
                        'created_at'   => $request->payment_date,
                    ]);
                }

                Log::info("Manual Onboarding Success: User ID {$user->id} by Admin ID " . Auth::id());

                return redirect()->back()->with('success', "Account for '{$user->name}' created and payment synced successfully.");
            });
        } catch (\Exception $e) {
            Log::error('Manual Onboarding Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->withInput()->withErrors(['error' => 'Onboarding failed: ' . $e->getMessage()]);
        }
    }
}
