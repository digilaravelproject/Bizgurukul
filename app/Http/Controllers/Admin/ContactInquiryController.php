<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactInquiry;
use Illuminate\Http\Request;

class ContactInquiryController extends Controller
{
    public function index()
    {
        $inquiries = ContactInquiry::latest()->paginate(15);
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
}
