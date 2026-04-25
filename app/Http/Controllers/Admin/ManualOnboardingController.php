<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Lead;
use App\Models\State;
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
     * Get leads for searchable dropdown.
     */
    public function getLeads(Request $request)
    {
        $search = $request->input('q');
        $leads = Lead::when($search, function($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
        })
        ->latest()
        ->limit(20)
        ->get(['id', 'name', 'email', 'mobile']);

        return response()->json($leads);
    }

    /**
     * Get specific lead details.
     */
    public function getLeadDetails($id)
    {
        $lead = Lead::with('sponsor:id,name,referral_code')->findOrFail($id);
        
        return response()->json([
            'id'                => $lead->id,
            'name'              => $lead->name,
            'email'             => $lead->email,
            'mobile'            => $lead->mobile,
            'gender'            => $lead->gender,
            'state_id'          => $lead->state_id,
            'referral_code'     => $lead->referral_code,
            'sponsor_name'      => $lead->sponsor ? $lead->sponsor->name : null,
            'bundle_id'         => $lead->product_preference['bundle_id'] ?? null,
        ]);
    }

    /**
     * Store the manual onboarding data and sync with production registration flow.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mode'               => 'required|in:lead,manual',
            'lead_id'            => 'required_if:mode,lead|nullable|exists:leads,id',
            'name'               => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email',
            'mobile'             => 'required|numeric|digits:10',
            'password'           => 'required|min:8',
            'gender'             => 'required|in:male,female,other',
            'state_id'           => 'required|exists:states,id',
            'bundle_id'          => 'required|exists:bundles,id',
            'referral_code'      => 'nullable|string|exists:users,referral_code',
            'payment_method'     => 'required|in:razorpay,cashfree,manual',
            'gateway_payment_id' => 'required|string|unique:payments,gateway_payment_id',
            'gateway_order_id'   => 'nullable|string',
            'utr_number'         => 'nullable|string',
            'amount'             => 'required|numeric|min:0',
            'payment_date'       => 'required|date',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                // 1. Resolve or Create Lead
                if ($request->mode === 'lead') {
                    $lead = Lead::findOrFail($request->lead_id);
                    // Ensure lead data matches request if it was edited
                    $lead->update([
                        'name'               => $request->name,
                        'email'              => $request->email,
                        'mobile'             => $request->mobile,
                        'gender'             => $request->gender,
                        'state_id'           => $request->state_id,
                        'product_preference' => ['bundle_id' => $request->bundle_id],
                        'referral_code'      => $request->referral_code,
                    ]);
                } else {
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
                }

                // 2. Prepare data for RegistrationService
                $verifyData = [
                    'gateway'             => $request->payment_method,
                    'is_webhook'          => true, // Skips verification signature checks
                    'lead_id'             => $lead->id,
                    'gateway_order_id'    => $request->gateway_order_id ?: 'MANUAL_ORD_' . time(),
                    'gateway_payment_id'  => $request->gateway_payment_id,
                    'coupon_code'         => null,
                ];

                // 3. Execute production registration sequence
                $user = $this->registrationService->verifyAndCompleteRegistration($verifyData);

                // 4. Update payment details (amount, date, UTR)
                $payment = $user->payments()->latest()->first();
                if ($payment) {
                    $payment->update([
                        'amount'       => $request->amount,
                        'total_amount' => $request->amount,
                        'status'       => 'success',
                        'utr_number'   => $request->utr_number,
                        'created_at'   => $request->payment_date,
                    ]);
                }

                Log::info("Manual Onboarding Success: User ID {$user->id} by Admin ID " . Auth::id());

                return redirect()->back()->with('success', "Account for '{$user->name}' created successfully.");
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
