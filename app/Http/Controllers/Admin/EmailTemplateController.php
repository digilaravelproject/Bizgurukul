<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::orderBy('name')->get();
        return view('admin.email-templates.index', compact('templates'));
    }

    public function edit(string $key)
    {
        $template = EmailTemplate::where('key', $key)->firstOrFail();
        return view('admin.email-templates.edit', compact('template'));
    }

    public function update(Request $request, string $key)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        try {
            EmailTemplate::updateByKey($key, [
                'subject' => $request->input('subject'),
                'body'    => $request->input('body'),
            ]);

            return redirect()->route('admin.email-templates.index')
                ->with('success', 'Email template updated successfully.');

        } catch (Exception $e) {
            Log::error('Email template update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update template.');
        }
    }
}
