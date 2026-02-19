<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentRepository
{
    /**
     * Create a new payment record
     */
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    /**
     * Find payment by Razorpay Order ID
     */
    public function findByRazorpayOrderId(string $orderId): ?Payment
    {
        return Payment::where('razorpay_order_id', $orderId)->first();
    }

    /**
     * Find payment by ID
     */
    public function findById(int $id): ?Payment
    {
        return Payment::find($id);
    }

    /**
     * Update payment status or details
     */
    public function update(Payment $payment, array $data): bool
    {
        return $payment->update($data);
    }

    /**
     * Get payments by user
     */
    public function getPaymentsByUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Payment::where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }
}
