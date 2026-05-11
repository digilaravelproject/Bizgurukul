<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\EmailService;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        return view('student.support.index');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        $user = Auth::user();

        // Pass to EmailService to handle the dual emails
        EmailService::sendSupportQuery($user, $request->subject, $request->message);

        return redirect()->route('student.support.index')
            ->with('success', 'Your message has been sent successfully! Our support team will get back to you shortly.');
    }
}