<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Bundle;
use App\Models\Payment;
use App\Models\User;
use App\Models\State;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
        $bundles = Bundle::where('is_published', 1)->get();
        $states = State::orderBy('name')->get();
        return view('admin.manual-onboarding', compact('bundles', 'states'));
    }

    /**
     * API: Search leads for auto-fill.
     */
    public function getLeads(Request $request)
    {
        $search = $request->query('q');
        $leads = Lead::where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'mobile']);

        return response()->json($leads);
    }

    /**
     * API: Get specific lead details.
     */
    public function getLeadDetails($id)
    {
        $lead = Lead::with('sponsor')->findOrFail($id);
        
        $bundleId = null;
        if ($lead->product_preference) {
            $pref = $lead->product_preference;
            $bundleId = $pref['bundle_id'] ?? null;
        }

        return response()->json([
            'lead' => $lead,
            'bundle_id' => $bundleId,
            'sponsor_name' => $lead->sponsor ? $lead->sponsor->name : 'No Sponsor'
        ]);
    }

    /**
     * Store the manual onboarding request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mode'               => 'required|in:lead,manual',
            'lead_id'            => 'required_if:mode,lead|nullable|exists:leads,id',
            'name'               => 'required|string|max:255',
            'email'              => 'required|email',
            'mobile'             => 'required|string|max:15',
            'password'           => 'required|min:8',
            'gender'             => 'required|in:male,female,other',
            'state_id'           => 'required|exists:states,id',
            'bundle_id'          => 'required|exists:bundles,id',
            'referral_code'      => 'nullable|string',
            'amount'             => 'required|numeric|min:0',
            'gateway_payment_id' => 'required|string',
            'gateway_order_id'   => 'nullable|string',
            'utr_number'         => 'nullable|string',
            'payment_date'       => 'required|date',
            'payment_method'     => 'required|string'
        ]);

        try {
            return DB::transaction(function () use ($validated) {
                // 1. Resolve or Create Lead (To satisfy RegistrationService requirements)
                if ($validated['mode'] === 'lead') {
                    $lead = Lead::findOrFail($validated['lead_id']);
                } else {
                    // Check if user already exists
                    if (User::where('email', $validated['email'])->exists()) {
                        throw new \Exception("User with this email already exists.");
                    }

                    // Create a temporary Lead to bridge into RegistrationService
                    $lead = Lead::create([
                        'name'               => $validated['name'],
                        'email'              => $validated['email'],
                        'mobile'             => $validated['mobile'],
                        'password'           => $validated['password'],
                        'referral_code'      => $validated['referral_code'],
                        'gender'             => $validated['gender'],
                        'state_id'           => $validated['state_id'],
                        'product_preference' => ['bundle_id' => $validated['bundle_id']],
                        'ip_address'         => request()->ip(),
                    ]);
                }

                // 2. Prepare Registration Data
                $regData = [
                    'lead_id'            => $lead->id,
                    'gateway'            => $validated['payment_method'],
                    'is_webhook'         => true, 
                    'gateway_order_id'   => $validated['gateway_order_id'] ?: 'MANUAL_ORD_' . time(),
                    'gateway_payment_id' => $validated['gateway_payment_id'],
                ];

                // 3. Execute production registration sequence
                $result = $this->registrationService->verifyAndCompleteRegistration($regData);

                // Fetch the payment record created by the service
                $user = is_a($result, User::class) ? $result : User::where('email', $lead->email)->first();
                $payment = Payment::where('user_id', $user->id)->latest()->first();

                if (!$payment) {
                    throw new \Exception("Registration sequence completed but no payment record was found.");
                }

                $paymentCreatedAt = Carbon::parse($validated['payment_date'])->setTimeFrom(now());

                // 4. Update payment details for 100% parity
                $payment->update([
                    'amount'             => $validated['amount'],
                    'total_amount'       => $validated['amount'],
                    'status'             => 'success',
                    'gateway_payment_id' => $validated['gateway_payment_id'],
                    'utr_number'         => $validated['utr_number'] ?: $validated['gateway_payment_id'],
                ]);

                $payment->created_at = $paymentCreatedAt;
                $payment->save();

                // Explicitly sync the user's created_at to the same timestamp
                if ($user) {
                    $user->created_at = $paymentCreatedAt;
                    $user->save();
                }

                // Explicitly sync any commission's created_at to the same timestamp
                $commission = \App\Models\AffiliateCommission::where('referred_user_id', $user->id)
                    ->where('reference_id', $validated['bundle_id'])
                    ->where('reference_type', \App\Models\Bundle::class)
                    ->first();
                if ($commission) {
                    $commission->created_at = $paymentCreatedAt;
                    $holdingHours = (int) \App\Models\Setting::get('commission_holding_hours', 24);
                    $commission->available_at = $paymentCreatedAt->copy()->addHours($holdingHours);
                    $commission->save();
                }

                Log::info("Manual Onboarding Success: User ID {$user->id} by Admin ID " . Auth::id());

                return redirect()->back()->with('success', "Account for '{$user->name}' onboarded successfully. Invoice is ready.");
            });
        } catch (\Exception $e) {
            Log::error('Manual Onboarding Failed', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id()
            ]);
            return redirect()->back()->with('error', "Onboarding Error: " . $e->getMessage())->withInput();
        }
    }
}
