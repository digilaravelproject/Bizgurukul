<?php

namespace App\Http\Controllers;

use App\Models\ContactInquiry;
use App\Models\Setting;
use App\Mail\ContactInquiryMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $inquiry = ContactInquiry::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);

            // Send Email to User
            Mail::to($inquiry->email)->send(new ContactInquiryMail($inquiry, 'user'));

            // Send Email to Admin
            $adminEmail = Setting::get('admin_notification_email') ?: Setting::get('company_email', config('mail.from.address'));
            if ($adminEmail) {
                Mail::to($adminEmail)->send(new ContactInquiryMail($inquiry, 'admin'));
            }

            return back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');

        } catch (Exception $e) {
            Log::error('Contact form submission error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
