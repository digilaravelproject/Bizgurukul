<?php

namespace App\Http\Controllers;

use App\Models\Community;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class CommunityController extends Controller
{
    public function index()
    {
        try {
            $communities = Community::active()
                ->orderBy('order_index')
                ->get()
                ->groupBy('group_name');

            return view('web.communities', compact('communities'));
        } catch (Exception $e) {
            Log::error("CommunityController Error [index]: " . $e->getMessage());
            return redirect()->route('home')->with('error', 'Unable to load communities at this time.');
        }
    }

    public function studentIndex()
    {
        try {
            $communities = Community::active()
                ->orderBy('order_index')
                ->get()
                ->groupBy('group_name');

            return view('student.communities', compact('communities'));
        } catch (Exception $e) {
            Log::error("CommunityController Error [studentIndex]: " . $e->getMessage());
            return redirect()->route('student.dashboard')->with('error', 'Unable to load communities at this time.');
        }
    }
}
