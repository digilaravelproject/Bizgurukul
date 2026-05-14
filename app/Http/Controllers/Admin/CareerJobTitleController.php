<?php

namespace App\Http\Controllers\Admin;

use App\Models\CareerJobTitle;
use Illuminate\Database\Eloquent\Model;

class CareerJobTitleController extends CareerJobMasterBaseController
{
    protected function getModel(): Model { return new CareerJobTitle(); }
    protected function getRouteName(): string { return 'career-titles'; }
    protected function getTitle(): string { return 'Job Title'; }
}
