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
        $query = Lead::with('sponsor');

        // Search by name, email, or mobile
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Filter by Date Range
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $perPage = $request->input('per_page', 20);
        $leads = $query->latest()->paginate($perPage);

        // Transform to include bundle info efficiently
        $leads->getCollection()->transform(function($lead) {
            // Product Info
            $bundleId = $lead->product_preference['bundle_id'] ?? null;
            if ($bundleId) {
                $bundle = Bundle::find($bundleId);
                $lead->product_name = $bundle ? $bundle->title : 'Unknown Bundle';
            } else {
                $lead->product_name = 'N/A';
            }
            
            return $lead;
        });

        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.leads.partials.leads_table', compact('leads'))->render(),
                'pagination' => view('components.admin.table.pagination', ['records' => $leads])->render()
            ]);
        }

        return view('admin.leads.index', compact('leads'));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['search', 'start_date', 'end_date']);
        $filename = 'leads_export_' . date('Y-m-d_H-i-s') . '.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LeadsExport($filters),
            $filename
        );
    }
}
