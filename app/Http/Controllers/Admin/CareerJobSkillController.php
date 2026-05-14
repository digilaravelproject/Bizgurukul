<?php

namespace App\Http\Controllers\Admin;

use App\Models\CareerJobSkill;
use Illuminate\Database\Eloquent\Model;

class CareerJobSkillController extends CareerJobMasterBaseController
{
    protected function getModel(): Model { return new CareerJobSkill(); }
    protected function getRouteName(): string { return 'career-skills'; }
    protected function getTitle(): string { return 'Job Skill'; }
}
