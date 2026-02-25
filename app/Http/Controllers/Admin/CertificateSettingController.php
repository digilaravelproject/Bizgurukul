<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class CertificateSettingController extends Controller
{
    public function index()
    {
        $templateData = Setting::where('key', 'certificate_template')->first();
        $templateUrl = $templateData && $templateData->value ? Storage::url($templateData->value) : null;

        return view('admin.certificates.settings', compact('templateUrl'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'certificate_template' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('certificate_template')) {
            $file = $request->file('certificate_template');

            // Delete old file if exists
            $oldSetting = Setting::where('key', 'certificate_template')->first();
            if ($oldSetting && $oldSetting->value && Storage::exists($oldSetting->value)) {
                Storage::delete($oldSetting->value);
            }

            // Store new file
            $path = $file->store('certificates', 'public');

            // Update setting
            Setting::updateOrCreate(
                ['key' => 'certificate_template'],
                ['value' => $path]
            );

            return redirect()->back()->with('success', 'Certificate template uploaded successfully.');
        }

        return redirect()->back()->with('error', 'Failed to upload certificate template.');
    }
}
