<?php

namespace App\Http\Controllers\Admin;

use App\Models\CareerJobSalary;
use Illuminate\Database\Eloquent\Model;

class CareerJobSalaryController extends CareerJobMasterBaseController
{
    protected function getModel(): Model { return new CareerJobSalary(); }
    protected function getRouteName(): string { return 'career-salaries'; }
    protected function getTitle(): string { return 'Job Salary'; }
}
