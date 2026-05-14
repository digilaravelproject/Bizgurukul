<?php

namespace App\Http\Controllers\Admin;

use App\Models\CareerJobExperience;
use Illuminate\Database\Eloquent\Model;

class CareerJobExperienceController extends CareerJobMasterBaseController
{
    protected function getModel(): Model { return new CareerJobExperience(); }
    protected function getRouteName(): string { return 'career-experiences'; }
    protected function getTitle(): string { return 'Job Experience'; }
}
