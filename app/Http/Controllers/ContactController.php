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
            Log::debug('Contact form submission started.', $request->all());

            $inquiry = ContactInquiry::create([
                'name'    => $request->name,
                'email'   => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);

            Log::debug('Contact inquiry saved to DB.', ['id' => $inquiry->id]);

            // Send Email to User
            try {
                Mail::to($inquiry->email)->send(new ContactInquiryMail($inquiry, 'user'));
                Log::debug('Email sent to user.');
            } catch (Exception $e) {
                Log::error('Failed to send email to user: ' . $e->getMessage());
            }

            // Send Email to Admin
            try {
                $adminEmail = Setting::get('admin_notification_email') ?: Setting::get('company_email', config('mail.from.address'));
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new ContactInquiryMail($inquiry, 'admin'));
                    Log::debug('Email sent to admin.', ['admin_email' => $adminEmail]);
                } else {
                    Log::warning('No admin email found for notification.');
                }
            } catch (Exception $e) {
                Log::error('Failed to send email to admin: ' . $e->getMessage());
            }

            return back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');

        } catch (Exception $e) {
            Log::error('Contact form submission error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->withInput()->with('error', 'Something went wrong. Please try again later.');
        }
    }
}
