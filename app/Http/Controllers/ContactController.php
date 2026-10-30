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
        // 1. Honeypot trap check - bots automatically fill hidden fields
        if ($request->filled('_hp_website')) {
            Log::warning('Contact form spam caught via honeypot field.', ['ip' => $request->ip()]);
            // Return fake success so bots do not retry or mutate
            return back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');
        }

        // 2. Submission timestamp check (reject if filled in under 3 seconds)
        if ($request->filled('_form_time')) {
            try {
                $formTime = (int) decrypt($request->_form_time);
                if ((time() - $formTime) < 3) {
                    Log::warning('Contact form spam caught via fast submission (< 3s).', [
                        'ip' => $request->ip(),
                        'elapsed_sec' => time() - $formTime
                    ]);
                    return back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');
                }
            } catch (Exception $e) {
                Log::warning('Invalid form token in contact submission.', ['ip' => $request->ip()]);
                return back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');
            }
        }

        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'subject' => 'required|string',
            'message' => 'required|string',
        ]);

        // 3. Spam content detection:
        // - Block Cyrillic / Russian characters (targeting Indian learning platform)
        // - Block URLs/links or domain extensions inside Name or Subject
        // - Block spam link shorteners
        $fullText = $request->name . ' ' . $request->subject . ' ' . $request->message;
        $isCyrillic = preg_match('/[\p{Cyrillic}]/u', $fullText);
        $hasUrlInName = preg_match('/(https?:\/\/|www\.|\.buzz|\.ru|\.xyz|\.top|\.click|\.link|\.site|tinyurl)/i', $request->name);
        $excessiveLinks = preg_match_all('/https?:\/\//i', $request->message) > 2;

        if ($isCyrillic || $hasUrlInName || $excessiveLinks) {
            Log::warning('Contact form spam caught via content filter (Cyrillic/URL).', [
                'ip' => $request->ip(),
                'name' => $request->name,
                'email' => $request->email,
                'is_cyrillic' => (bool)$isCyrillic,
                'has_url_in_name' => (bool)$hasUrlInName,
            ]);
            // Return fake success to safely drop spam without sending emails or alerting bots
            return back()->with('success', 'Your message has been sent successfully. We will get back to you soon!');
        }

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
