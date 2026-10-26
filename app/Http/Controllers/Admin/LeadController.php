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

        // Transform to include current/upgraded bundle info efficiently
        $emails = $leads->pluck('email')->filter()->toArray();
        $mobiles = $leads->pluck('mobile')->filter()->toArray();

        $convertedUsers = User::where(function($q) use ($emails, $mobiles) {
                if (!empty($emails)) {
                    $q->whereIn('email', $emails);
                }
                if (!empty($mobiles)) {
                    $q->orWhereIn('mobile', $mobiles);
                }
            })
            ->with(['bundles' => function($q) {
                $q->orderByDesc('bundles.preference_index')->orderByDesc('payments.id');
            }])
            ->get();

        $userByEmail = $convertedUsers->keyBy('email');
        $userByMobile = $convertedUsers->filter(fn($u) => !empty($u->mobile))->keyBy('mobile');

        $allBundles = Bundle::all()->keyBy('id');

        $leads->getCollection()->transform(function($lead) use ($userByEmail, $userByMobile, $allBundles) {
            $user = $userByEmail->get($lead->email) ?? ($lead->mobile ? $userByMobile->get($lead->mobile) : null);

            if ($user && $user->current_bundle) {
                $lead->product_name = $user->current_bundle->title;
                $lead->is_converted = true;
            } else {
                $bundleId = $lead->product_preference['bundle_id'] ?? null;
                $bundle = $bundleId ? $allBundles->get($bundleId) : null;
                $lead->product_name = $bundle ? $bundle->title : 'N/A';
                $lead->is_converted = false;
            }
            
            return $lead;
        });

        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.leads.partials.leads_table', compact('leads'))->render(),
                'pagination' => view('components.admin.table.pagination', ['records' => $leads])->render()
            ])->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
              ->header('Pragma', 'no-cache')
              ->header('Expires', '0');
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
