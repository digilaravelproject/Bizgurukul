<?php
use Illuminate\Http\Request;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Request::capture()); // Just to boot the app

// 1. Get a valid Bundle
$bundle = \App\Models\Bundle::first();
if (!$bundle) {
    die("No Bundles found in DB.\n");
}

// 2. Clear any old test data
$email = 'testwebhook_' . time() . '@example.com';
\App\Models\User::where('email', $email)->delete();
\App\Models\Lead::where('email', $email)->delete();

// 3. Create a Lead as if they completed Phase 1
$lead = \App\Models\Lead::create([
    'name' => 'Auto Test User',
    'email' => $email,
    'mobile' => '9999999' . rand(100, 999),
    'password' => \Illuminate\Support\Facades\Hash::make('password123'),
    'product_preference' => ['bundle_id' => $bundle->id],
    'ip_address' => '127.0.0.1'
]);

echo "Created Lead ID: {$lead->id}\n";

// 4. Create Razorpay Payload
$payloadArray = [
    'event' => 'payment.captured',
    'payload' => [
        'payment' => [
            'entity' => [
                'id' => 'pay_test_' . rand(1000, 9999),
                'order_id' => 'order_test_' . rand(1000, 9999),
                'amount' => $bundle->website_price * 100, // Amount in paise
                'status' => 'captured',
                'notes' => [
                    'lead_id' => (string) $lead->id,
                    'coupon_code' => ''
                ]
            ]
        ]
    ]
];
$payloadJson = json_encode($payloadArray);

// 5. Generate Signature
$secret = config('services.razorpay.webhook_secret');
if (empty($secret)) {
    die("ERROR: RAZORPAY_WEBHOOK_SECRET is not set in .env\n");
}
$signature = hash_hmac('sha256', $payloadJson, $secret);

// 6. Simulate API Request to Webhook Controller
echo "Simulating Webhook Request...\n";
$request = Request::create(
    '/webhook/razorpay', 
    'POST', 
    [], 
    [], 
    [], 
    [
        'HTTP_X_RAZORPAY_SIGNATURE' => $signature,
        'CONTENT_TYPE' => 'application/json'
    ], 
    $payloadJson
);

$response = $kernel->handle($request);

echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Body: " . $response->getContent() . "\n";

// 7. Verification
if ($response->getStatusCode() === 200) {
    $user = \App\Models\User::where('email', $email)->first();
    if ($user) {
        echo "SUCCESS: User '{$user->name}' was successfully created in the database!\n";
        
        // Cleanup test data
        \App\Models\Payment::where('user_id', $user->id)->delete();
        $user->delete();
        echo "Test data cleaned up.\n";
    } else {
        echo "FAILED: Response was 200 OK, but User was NOT found in the database.\n";
    }
} else {
    echo "FAILED: Webhook endpoint returned an error.\n";
}
