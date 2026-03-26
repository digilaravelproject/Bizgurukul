<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Models\Bundle;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::query();

        // Search by name, email, or mobile
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Filter by Status (Lead table status column if exists, otherwise assume all are pending)
        // Leads are deleted upon conversion, so all current ones are 'Pending'

        $leads = $query->latest()->paginate(20);

        // Transform to include bundle and sponsor info
        $leads->getCollection()->transform(function($lead) {
            // Product Info
            $bundleId = $lead->product_preference['bundle_id'] ?? null;
            if ($bundleId) {
                $bundle = Bundle::find($bundleId);
                $lead->product_name = $bundle ? $bundle->title : 'Unknown Bundle';
            } else {
                $lead->product_name = 'N/A';
            }

            // Sponsor Info
            $lead->sponsor = User::where('referral_code', $lead->referral_code)->first();
            
            return $lead;
        });

        if ($request->ajax()) {
            return view('admin.leads.partials.leads_table', compact('leads'))->render();
        }

        return view('admin.leads.index', compact('leads'));
    }
}
