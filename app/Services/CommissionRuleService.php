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
}
