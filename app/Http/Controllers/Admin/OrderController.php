<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with filters.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'bundle', 'course', 'paymentable']);

        // Applying Search
        if ($search = $request->input('search')) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            })->orWhere('razorpay_order_id', 'like', "%{$search}%")
              ->orWhere('razorpay_payment_id', 'like', "%{$search}%");
        }

        // Applying Date Filter
        $filter = $request->input('filter', 'all_time');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($filter === 'custom' && $startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        } else {
            switch ($filter) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case '7_days':
                    $query->where('created_at', '>=', Carbon::now()->subDays(7));
                    break;
                case '30_days':
                    $query->where('created_at', '>=', Carbon::now()->subDays(30));
                    break;
                case 'all_time':
                default:
                    // no date filter
                    break;
            }
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return view('admin.orders.partials.history_table', compact('orders'))->render();
        }

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display a specific invoice for an order.
     */
    public function invoice($id)
    {
        try {
            $invoice = Payment::with(['bundle', 'course', 'paymentable', 'user'])
                ->findOrFail($id);

            // Fetch company settings for invoice header
            $settings = (object) [
                'site_name' => Setting::get('site_name', 'Skills Pehle'),
                'company_address' => Setting::get('company_address', '123 Business Park, Tech Hub'),
                'company_city' => Setting::get('company_city', 'New Delhi'),
                'company_state' => Setting::get('company_state', 'Delhi'),
                'company_zip' => Setting::get('company_zip', '110001'),
                'company_email' => Setting::get('company_email', 'support@Skills Pehle.com'),
                'company_phone' => Setting::get('company_phone', '+91 9876543210'),
                'company_logo' => Setting::get('company_logo', null),
            ];

            return view('student.invoices.show', compact('invoice', 'settings'));

        } catch (ModelNotFoundException $e) {
            Log::warning('Invoice access attempt for non-existent invoice ID ' . $id);
            return abort(404, 'Invoice not found.');
        } catch (Exception $e) {
            Log::error('Error loading invoice ID ' . $id . ' in admin: ' . $e->getMessage());
            return back()->with('error', 'Unable to load the invoice right now. Please try again.');
        }
    }
}
