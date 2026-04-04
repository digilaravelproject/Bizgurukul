<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrdersExport;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with filters.
     */
    public function index(Request $request)
    {
        // Per Page
        $perPage = $request->input('per_page', 20);
        if (!in_array($perPage, [20, 30, 50, 100, 200])) {
            $perPage = 20;
        }

        $query = Payment::with(['user.referrer', 'bundle', 'course', 'paymentable']);

        // Applying Status Filter
        $status = $request->input('status');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Applying Search
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%");
                })->orWhere('razorpay_order_id', 'like', "%{$search}%")
                  ->orWhere('gateway_order_id', 'like', "%{$search}%")
                  ->orWhere('razorpay_payment_id', 'like', "%{$search}%")
                  ->orWhere('gateway_payment_id', 'like', "%{$search}%");
            });
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

        $orders = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'table' => view('admin.orders.partials.history_table', compact('orders'))->render(),
                'pagination' => view('components.admin.table.pagination', ['records' => $orders])->render()
            ]);
        }

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Export orders to Excel (using professional Export class).
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'filter', 'start_date', 'end_date']);
        $filename = "orders_" . now()->format('Y-m-d_H-i-s') . ".xlsx";

        return Excel::download(new OrdersExport($filters), $filename);
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

            $downloadUrl = route('admin.orders.invoice.download', $id);

            return view('student.invoices.show', compact('invoice', 'settings', 'downloadUrl'));

        } catch (ModelNotFoundException $e) {
            Log::warning('Invoice access attempt for non-existent invoice ID ' . $id);
            return abort(404, 'Invoice not found.');
        } catch (Exception $e) {
            Log::error('Error loading invoice ID ' . $id . ' in admin: ' . $e->getMessage());
            return back()->with('error', 'Unable to load the invoice right now. Please try again.');
        }
    }

    /**
     * Download a specific invoice for an order.
     */
    public function downloadInvoice($id)
    {
        try {
            $invoice = Payment::with(['bundle', 'course', 'paymentable', 'user'])
                ->findOrFail($id);

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

            $is_pdf = true;
            $pdf = Pdf::loadView('student.invoices.show', compact('invoice', 'settings', 'is_pdf'));
            $safeFilename = str_replace(['#', '/', '\\'], '-', $invoice->invoice_no);
            return $pdf->download('Invoice-' . $safeFilename . '.pdf');

        } catch (ModelNotFoundException $e) {
            return abort(404, 'Invoice not found.');
        } catch (Exception $e) {
            Log::error('Error downloading invoice ID ' . $id . ' in admin: ' . $e->getMessage());
            return back()->with('error', 'Unable to download the invoice right now. Please try again.');
        }
    }
}
