<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactInquiryReplyMail;

class ContactInquiryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        
        $query = ContactInquiry::latest();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }
        
        $inquiries = $query->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'table' => view('admin.contact-inquiries.partials.table', compact('inquiries'))->render(),
                'pagination' => view('admin.contact-inquiries.partials.pagination', compact('inquiries'))->render(),
            ]);
        }

        return view('admin.contact-inquiries.index', compact('inquiries'));
    }

    public function show($id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        return view('admin.contact-inquiries.show', compact('inquiry'));
    }

    public function destroy($id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        $inquiry->delete();

        return redirect()->route('admin.contact-inquiries.index')->with('success', 'Inquiry deleted successfully.');
    }

    public function markReplied($id)
    {
        $inquiry = ContactInquiry::findOrFail($id);
        $inquiry->update(['is_replied' => true]);

        return back()->with('success', 'Inquiry marked as replied.');
    }

    public function sendReply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:5',
        ]);

        try {
            $inquiry = ContactInquiry::findOrFail($id);
            
            // Send Email
            Mail::to($inquiry->email)->send(new ContactInquiryReplyMail($inquiry, $request->message));
            
            // Update Status
            $inquiry->update(['is_replied' => true]);

            return response()->json([
                'status' => true,
                'message' => 'Reply sent successfully and inquiry marked as replied.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send reply: ' . $e->getMessage(),
            ], 500);
        }
    }
}
