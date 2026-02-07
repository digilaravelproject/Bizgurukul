<?php

namespace App\Repositories;

use App\Models\CommissionRule;

class CommissionRuleRepository
{
    protected $model;

    public function __construct(CommissionRule $model)
    {
        $this->model = $model;
    }

    public function getPaginatedRules($perPage = 10)
    {
        return $this->model->with(['affiliate', 'product'])->latest()->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function delete($id)
    {
        $rule = $this->model->find($id);
        if ($rule) {
            return $rule->delete();
        }
        return false;
    }

    public function find($id) {
        return $this->model->find($id);
    }
}
