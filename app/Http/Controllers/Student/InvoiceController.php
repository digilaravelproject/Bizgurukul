<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Payment;
use App\Models\Setting;
use Exception;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the user's invoices.
     */
    public function index()
    {
        try {
            $user = Auth::user();

            // Eager load relationships ('bundle', 'course', 'paymentable') to prevent N+1 queries.
            // This makes the app much faster and more scalable.
            $invoices = Payment::with(['bundle', 'course', 'paymentable'])
                ->where('user_id', $user->id)
                ->where('status', 'success')
                ->latest()
                ->paginate(10);

            return view('student.invoices.index', compact('invoices'));

        } catch (Exception $e) {
            // Log the actual error for developers
            Log::error('Error fetching invoices for user ' . Auth::id() . ': ' . $e->getMessage());

            // Return a user-friendly error message
            return back()->with('error', 'Something went wrong while fetching your invoices. Please try again later.');
        }
    }

    /**
     * Display a specific invoice.
     */
    public function show($id)
    {
        try {
            $user = Auth::user();

            // Eager load relationships here as well
            $invoice = Payment::with(['bundle', 'course', 'paymentable', 'user'])
                ->where('user_id', $user->id)
                ->where('id', $id)
                ->where('status', 'success')
                ->firstOrFail();

            // Fetch company settings for invoice header
            $settings = (object) [
                'site_name' => Setting::get('site_name', 'BizGurukul'),
                'company_address' => Setting::get('company_address', '123 Business Park, Tech Hub'),
                'company_city' => Setting::get('company_city', 'New Delhi'),
                'company_state' => Setting::get('company_state', 'Delhi'),
                'company_zip' => Setting::get('company_zip', '110001'),
                'company_email' => Setting::get('company_email', 'support@bizgurukul.com'),
                'company_phone' => Setting::get('company_phone', '+91 9876543210'),
                'company_logo' => Setting::get('company_logo', null),
            ];

            return view('student.invoices.show', compact('invoice', 'settings'));

        } catch (ModelNotFoundException $e) {
            // If the invoice ID doesn't exist or doesn't belong to the user
            Log::warning('Unauthorized or missing invoice access attempt by user ' . Auth::id() . ' for invoice ID ' . $id);
            return abort(404, 'Invoice not found or you do not have permission to view it.');

        } catch (Exception $e) {
            // Catch any other general exceptions (e.g., DB connection issues)
            Log::error('Error loading invoice ID ' . $id . ' for user ' . Auth::id() . ': ' . $e->getMessage());
            return back()->with('error', 'Unable to load the invoice right now. Please try again.');
        }
    }
}
