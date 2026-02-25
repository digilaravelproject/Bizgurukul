<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ $subject ?? 'Notification' }}</title>
    <style>
        /* Reset */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #0f1117;
            color: #e2e8f0;
            -webkit-font-smoothing: antialiased;
            line-height: 1.6;
        }
        .email-wrapper {
            background-color: #0f1117;
            padding: 40px 16px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #1a1d2e;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(99,102,241,0.2);
            box-shadow: 0 25px 50px rgba(0,0,0,0.5);
        }
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #1e2035 0%, #16192a 100%);
            border-bottom: 1px solid rgba(99,102,241,0.15);
            padding: 32px 40px;
            text-align: center;
        }
        .email-logo {
            max-height: 48px;
            max-width: 180px;
            object-fit: contain;
        }
        .email-logo-text {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }
        .email-logo-text span {
            color: #6366f1;
        }
        /* Body */
        .email-body {
            padding: 40px;
        }
        .email-body p {
            color: #cbd5e1;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 16px;
        }
        .email-body strong {
            color: #f1f5f9;
        }
        /* Info Box */
        .email-body div[style*="background:#f8fafc"] {
            background: #1e2340 !important;
            border-radius: 10px;
            padding: 20px !important;
            margin: 24px 0 !important;
            font-size: 14px;
        }
        .email-body div[style*="background:#f8fafc"] p {
            color: #94a3b8 !important;
            font-size: 14px;
            margin-bottom: 8px !important;
        }
        .email-body div[style*="background:#f8fafc"] p:last-child {
            margin-bottom: 0 !important;
        }
        .email-body div[style*="background:#fef9ec"] {
            background: #1e1e10 !important;
            border-radius: 10px;
        }
        .email-body div[style*="background:#f0fdf4"] {
            background: #0d1f15 !important;
            border-radius: 10px;
        }
        .email-body div[style*="background:#f1f5f9"] {
            background: #1e2340 !important;
            border-radius: 10px;
        }
        /* Button */
        .email-body a[style*="background:#6366f1"],
        .email-body a[style*="background:#ef4444"] {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
            display: inline-block !important;
            padding: 14px 32px !important;
            border-radius: 10px !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            text-decoration: none !important;
            letter-spacing: 0.3px;
        }
        /* Footer */
        .email-footer {
            background: #13161f;
            border-top: 1px solid rgba(99,102,241,0.1);
            padding: 28px 40px;
            text-align: center;
        }
        .email-footer p {
            color: #475569;
            font-size: 12px;
            line-height: 1.6;
            margin-bottom: 4px;
        }
        .email-footer a {
            color: #6366f1;
            text-decoration: none;
        }
        .email-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(99,102,241,0.3), transparent);
            margin: 8px 0;
        }
        /* Responsive */
        @media (max-width: 600px) {
            .email-header,
            .email-body,
            .email-footer { padding: 24px 20px !important; }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-container">

        {{-- Header --}}
        <div class="email-header">
            @php
                $logo = \App\Models\Setting::get('company_logo');
                $siteName = \App\Models\Setting::get('site_name', config('app.name'));
            @endphp
            @if($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="{{ $siteName }}" class="email-logo">
            @else
                <div class="email-logo-text">{{ $siteName }}</div>
            @endif
        </div>

        {{-- Body --}}
        <div class="email-body">
            {!! $templateBody !!}
        </div>

        {{-- Footer --}}
        <div class="email-footer">
            <div class="email-divider"></div>
            <p style="margin-top:12px;">
                &copy; {{ date('Y') }} <strong>{{ $siteName }}</strong>. All rights reserved.
            </p>
            @php
                $address = trim(implode(', ', array_filter([
                    \App\Models\Setting::get('company_address'),
                    \App\Models\Setting::get('company_city'),
                    \App\Models\Setting::get('company_state'),
                ])));
            @endphp
            @if($address)
                <p>{{ $address }}</p>
            @endif
            @php $supportEmail = \App\Models\Setting::get('company_email'); @endphp
            @if($supportEmail)
                <p>Support: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a></p>
            @endif
            <p style="margin-top:8px; font-size:11px;">This is an automated email. Please do not reply directly to this message.</p>
        </div>

    </div>
</div>
</body>
</html>
