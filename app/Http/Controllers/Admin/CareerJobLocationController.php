<?php

namespace App\Http\Controllers\Admin;

use App\Models\CareerJobLocation;
use Illuminate\Database\Eloquent\Model;

class CareerJobLocationController extends CareerJobMasterBaseController
{
    protected function getModel(): Model { return new CareerJobLocation(); }
    protected function getRouteName(): string { return 'career-locations'; }
    protected function getTitle(): string { return 'Job Location'; }
}
