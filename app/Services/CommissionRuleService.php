<?php

namespace App\Services;

use App\Repositories\CommissionRuleRepository;
use App\Models\Course;
use App\Models\Bundle;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CommissionRuleService
{
    protected $ruleRepo;

    public function __construct(CommissionRuleRepository $ruleRepo)
    {
        $this->ruleRepo = $ruleRepo;
    }

    public function getRules()
    {
        return $this->ruleRepo->getPaginatedRules();
    }

    public function createRule(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // Formatting Data for Polymorphic Relation
                $insertData = [
                    'affiliate_id' => $data['affiliate_id'] ?? null,
                    'commission_type' => $data['commission_type'],
                    'amount' => $data['amount'],
                ];

                if (!empty($data['product_type']) && !empty($data['product_id'])) {
                    $insertData['product_type'] = $data['product_type'] === 'course' ? Course::class : Bundle::class;
                    $insertData['product_id'] = $data['product_id'];
                } else {
                    $insertData['product_type'] = null;
                    $insertData['product_id'] = null;
                }

                return $this->ruleRepo->create($insertData);
            } catch (Exception $e) {
                Log::error("CommissionRuleService Error: " . $e->getMessage());
                throw new Exception("Failed to create commission rule.");
            }
        });
    }

    public function deleteRule($id)
    {
        try {
            return $this->ruleRepo->delete($id);
        } catch (Exception $e) {
            Log::error("CommissionRuleService Error: " . $e->getMessage());
            throw new Exception("Failed to delete rule.");
        }
    }

    /**
     * Calculate Commission for a specific affiliate and product.
     * Hierarchy:
     * 1. Specific User + Specific Product
     * 2. Specific User + All Products
     * 3. Specific Product + Any User
     * 4. Global Settings (Fallback)
     */
    public function calculateCommission($affiliateId, $product)
    {
        // 1. Specific User + Specific Product
        $rule = \App\Models\CommissionRule::where('affiliate_id', $affiliateId)
            ->where('product_type', get_class($product))
            ->where('product_id', $product->id)
            ->first();

        // 2. Specific User + All Products
        if (!$rule) {
            $rule = \App\Models\CommissionRule::where('affiliate_id', $affiliateId)
                ->whereNull('product_id')
                ->first();
        }

        // 3. Specific Product + Any User
        if (!$rule) {
            $rule = \App\Models\CommissionRule::whereNull('affiliate_id')
                ->where('product_type', get_class($product))
                ->where('product_id', $product->id)
                ->first();
        }

        // Return calculated amount if rule found
        if ($rule) {
            if ($rule->commission_type === 'percentage') {
                return ($product->price * $rule->amount) / 100;
            } else {
                return $rule->amount;
            }
        }

        // 4. Fallback to Global Settings
        $globalType = \App\Models\Setting::get('referral_commission_type', 'fixed');
        $globalAmount = (float) \App\Models\Setting::get('referral_commission_amount', 0);

        if ($globalType === 'percentage') {
            return ($product->price * $globalAmount) / 100;
        }

        return $globalAmount;
    }
}
