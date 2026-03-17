<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_no }}</title>

    @if(!isset($is_pdf) || !$is_pdf)
    <!-- Importing your custom fonts normally -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @endif

    <style>
        :root {
            --color-primary: #F7941D;
            --color-secondary: #D04A02;
            --color-bg-body: #FFF8F0;
            --color-bg-card: #FFFFFF;
            --color-text-main: #2D2D2D;
            --color-text-muted: #555555;
        }

        body {
            font-family: @if(isset($is_pdf) && $is_pdf) 'DejaVu Sans', sans-serif @else 'Outfit', sans-serif @endif;
            color: var(--color-text-main);
            line-height: @if(isset($is_pdf) && $is_pdf) 1.2 @else 1.5 @endif;
            padding: @if(isset($is_pdf) && $is_pdf) 0 @else 40px 20px @endif;
            margin: 0;
            background-color: @if(isset($is_pdf) && $is_pdf) #fff @else var(--color-bg-body) @endif;
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
            width: 100%;
        }

        @if(isset($is_pdf) && $is_pdf)
        * {
            font-family: 'DejaVu Sans', sans-serif !important;
        }
        @endif

        .invoice-wrapper {
            @if(isset($is_pdf) && $is_pdf)
                width: 100%;
                margin: 0;
                padding: 0;
                border: none;
            @else
                max-width: 900px;
                width: auto;
                margin: 20px auto;
                padding: 40px;
                border-top: 6px solid var(--color-primary);
            @endif
            background: var(--color-bg-card);
            box-shadow: @if(isset($is_pdf) && $is_pdf) none @else 0 10px 25px rgba(0, 0, 0, 0.05) @endif;
            border-radius: @if(isset($is_pdf) && $is_pdf) 0 @else 4px @endif;
            overflow: visible;
        }

        /* --- Header Section --- */
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
        }

        .logo-container img {
            max-height: 70px;
            /* Adjusted for better logo visibility */
            width: auto;
            object-fit: contain;
        }

        .logo-container h1 {
            margin: 0;
            color: var(--color-primary);
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .company-details {
            text-align: right;
            line-height: 1.5;
            color: var(--color-text-muted);
            font-size: 13px;
        }

        .company-details strong {
            color: var(--color-text-main);
            font-size: 15px;
            display: inline-block;
            margin-bottom: 4px;
        }

        /* --- Invoice Meta Info --- */
        .invoice-meta-box {
            background-color: #fcfcfc;
            padding: 15px 20px;
            border: 1px solid #eaeaea;
            border-radius: 6px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .invoice-meta-box div {
            font-size: 14px;
            color: var(--color-text-muted);
        }

        .invoice-meta-box strong {
            color: var(--color-text-main);
            font-weight: 600;
        }

        .status-badge {
            background-color: #10b981;
            /* Professional Green for PAID */
            color: white;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-left: 5px;
        }

        /* --- Billed To Section --- */
        .billed-to {
            margin-bottom: 30px;
            line-height: 1.5;
            color: var(--color-text-muted);
        }

        .billed-to h4 {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--color-text-main);
            border-bottom: 2px solid #eaeaea;
            padding-bottom: 5px;
            display: inline-block;
        }

        /* --- Table Section --- */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #eaeaea;
        }

        th,
        td {
            border: 1px solid #eaeaea;
            padding: 14px;
            text-align: center;
        }

        th {
            background-color: #fafafa;
            font-weight: 600;
            color: var(--color-text-main);
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        td {
            color: var(--color-text-muted);
        }

        td strong {
            color: var(--color-text-main);
            font-weight: 600;
        }

        td.text-left,
        th.text-left {
            text-align: left;
        }

        td.text-right,
        th.text-right {
            text-align: right;
        }

        /* Tax Row Specific Styles */
        tr.tax-row td {
            padding: 0;
            background-color: #fafafa;
        }

        .tax-flex-container {
            display: flex;
            width: 100%;
            align-items: stretch;
            /* Make all columns equal height */
        }

        .tax-col {
            flex: 1;
            padding: 12px;
            border-right: 1px solid #eaeaea;
            text-align: center;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tax-col:last-child {
            border-right: none;
        }

        .tax-label-col {
            flex: 1.2;
            text-align: right;
            font-weight: 600;
            color: var(--color-text-main);
            justify-content: flex-end;
            padding-right: 20px;
        }

        /* --- Footer Rows --- */
        .declaration-row td,
        .computer-generated-row td {
            text-align: center;
            padding: 15px;
            background-color: #fafafa;
            font-size: 13px;
            color: var(--color-text-main);
        }

        .computer-generated-row td {
            font-weight: 600;
            border-top: none;
        }

        /* --- Actions (Print Button) --- */
        .bottom-actions {
            text-align: center;
            margin-top: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 15px;
            font-size: 13px;
            color: var(--color-text-muted);
        }

        .print-btn {
            background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-secondary) 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            transition: opacity 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(247, 148, 29, 0.3);
        }

        .print-btn:hover {
            opacity: 0.9;
        }

        /* --- Print Media Query --- */
        @page {
            size: A4;
            margin: 15mm;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 11pt; /* Better for A4 */
                width: 210mm;
                height: 297mm;
            }

            .invoice-wrapper {
                box-shadow: none !important;
                border: none !important;
                margin: 0 auto !important;
                padding: 15mm !important; /* Balanced padding on ALL sides */
                width: 180mm !important; /* Centered width */
                max-width: 180mm !important;
                box-sizing: border-box;
                display: block;
            }

            .header-top {
                margin-bottom: 15px; /* Reduced gap */
            }

            .invoice-meta-box {
                margin-bottom: 15px; /* Reduced gap */
                padding: 10px 15px; /* Compact padding */
                background-color: #fcfcfc !important;
                border: 1px solid #eaeaea !important;
            }

            .billed-to {
                margin-bottom: 15px; /* Reduced gap */
            }

            table {
                margin-bottom: 10px; /* Reduced gap */
            }

            th, td {
                padding: 8px 10px; /* More compact cells */
            }

            .bottom-actions {
                display: none;
            }

            /* Ensure background colors print properly */
            th,
            .tax-row td,
            .declaration-row td,
            .computer-generated-row td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background-color: #fafafa !important;
            }
        }

        /* --- Responsive Media Query --- */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            .invoice-wrapper {
                padding: 20px;
            }

            .header-top {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .company-details {
                text-align: center;
                margin-top: 20px;
            }

            .invoice-meta-box {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .invoice-meta-box div {
                width: 100%;
                display: flex;
                justify-content: space-between;
                border-bottom: 1px solid #f0f0f0;
                padding-bottom: 5px;
            }

            .invoice-meta-box div:last-child {
                border-bottom: none;
            }

            /* Responsive Table */
            .table-responsive {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            table {
                font-size: 12px;
                min-width: 600px; /* Allow horizontal scroll for table stability */
            }

            .tax-flex-container {
                flex-wrap: wrap;
            }

            .tax-col {
                flex: none;
                width: 50%;
                border-bottom: 1px solid #eaeaea;
            }

            .tax-col:nth-child(even) {
                border-right: none;
            }

            .tax-label-col {
                width: 100%;
                justify-content: center;
                padding-right: 0;
                background-color: #f5f5f5;
            }
        }
    </style>
</head>

<body>

    @php
        // Map 'success' status to 'PAID'
        $rawStatus = strtolower($invoice->status ?? 'paid');
        $displayStatus = $rawStatus === 'success' ? 'PAID' : strtoupper($rawStatus);

        // Calculation Logic ensuring values are properly formatted
        $totalAmount = $invoice->total_amount ?? $invoice->amount;

        // If tax details exist, calculate subtotal and tax.
        // Defaulting to 18% reverse calculation if exact values are missing.
        if (isset($invoice->tax_amount) && $invoice->tax_amount > 0) {
            $taxAmount = $invoice->tax_amount;
            $subTotal = $totalAmount - $taxAmount;
        } else {
            $subTotal = $totalAmount / 1.18;
            $taxAmount = $totalAmount - $subTotal;
        }

        $cgst = $taxAmount / 2;
        $sgst = $taxAmount / 2;
    @endphp

    <div class="invoice-wrapper">

        <!-- Header -->
        <table style="width: 100%; margin-bottom: 30px; border: none; border-collapse: collapse;">
            <tr style="border: none;">
                <td style="border: none; text-align: left; padding: 0; vertical-align: top; width: 50%;">
                    <div class="logo-container">
                        @if (isset($settings->company_logo) && $settings->company_logo)
                            @php
                                $logoPath = (isset($is_pdf) && $is_pdf) ? public_path('storage/' . $settings->company_logo) : asset('storage/' . $settings->company_logo);
                            @endphp
                            <img src="{{ $logoPath }}" alt="{{ $settings->site_name ?? 'Logo' }}" style="max-height: 70px; width: auto; object-fit: contain;">
                        @else
                            <!-- Fallback if no logo -->
                            <h1 style="margin: 0; color: #F7941D; font-size: 28px; font-weight: 700;">{{ $settings->site_name ?? 'SKILLS PEHLE' }}</h1>
                        @endif
                    </div>
                </td>
                <td style="border: none; text-align: right; padding: 0; vertical-align: top; width: 50%;">
                    <div style="line-height: 1.5; color: #555555; font-size: 13px;">
                        <strong style="color: #2D2D2D; font-size: 15px; margin-bottom: 4px; display: inline-block;">{{ strtoupper($settings->site_name ?? 'SKILLS PEHLE PRIVATE LIMITED') }}</strong><br>
                        Regd. Add: {{ $settings->company_address ?? '123 Business Park, Tech Hub' }}<br>
                        {{ $settings->company_city ?? 'Mumbai' }}<br>
                        {{ $settings->company_state ?? 'Maharashtra' }}, India - {{ $settings->company_zip ?? '400001' }}<br>
                        GSTIN: {{ $settings->company_gstin ?? '27HCHPS9578D1ZS' }}<br>
                        State Name: {{ $settings->company_state ?? 'Maharashtra' }}, Code : 27<br>
                        PAN: {{ $settings->company_pan ?? 'HCHPS9578D' }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Invoice Meta Data -->
        <table style="width: 100%; border-collapse: separate; border-spacing: 0; background-color: #fcfcfc; border: 1px solid #eaeaea; border-radius: 6px; margin-bottom: 30px; font-size: 13px; color: #555555;">
            <tr style="border: none;">
                <td style="padding: 15px 20px; border-right: 1px solid #eaeaea; border-bottom: none; border-top: none; border-left: none; width: 25%; text-align: left; vertical-align: middle;">
                    <strong style="color: #2D2D2D; display: block; margin-bottom: 3px;">Invoice Number:</strong> {{ $invoice->invoice_no }}
                </td>
                <td style="padding: 15px 20px; border-right: 1px solid #eaeaea; border-bottom: none; border-top: none; border-left: none; width: 25%; text-align: left; vertical-align: middle;">
                    <strong style="color: #2D2D2D; display: block; margin-bottom: 3px;">Order ID:</strong> {{ $invoice->razorpay_order_id ?? $invoice->id }}
                </td>
                <td style="padding: 15px 20px; border-right: 1px solid #eaeaea; border-bottom: none; border-top: none; border-left: none; width: 25%; text-align: left; vertical-align: middle;">
                    <strong style="color: #2D2D2D; display: block; margin-bottom: 3px;">Invoice Date:</strong> {{ $invoice->created_at->format('d-m-Y') }}
                </td>
                <td style="padding: 15px 20px; border: none; width: 25%; text-align: left; vertical-align: middle;">
                    <strong style="color: #2D2D2D; display: block; margin-bottom: 3px;">Status:</strong>
                    <span class="status-badge" style="background-color: #10b981; color: white; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; letter-spacing: 0.5px;">{{ $displayStatus }}</span>
                </td>
            </tr>
        </table>

        <!-- Billed To -->
        <div class="billed-to">
            <h4>Invoiced To</h4>
            <strong>{{ $invoice->user->name ?? 'Customer Name' }}</strong><br>
            {{ $invoice->user->email }}<br>
            {{ $invoice->user->mobile ?? '' }}
        </div>

        <!-- Items Table -->
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th class="text-left" style="width: 40%;">Description</th>
                        <th style="width: 15%;">Qty</th>
                        <th style="width: 20%;">HSN/SAC</th>
                        <th class="text-right" style="width: 25%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Item Row -->
                    <tr>
                        <td class="text-left">
                            @if ($invoice->bundle)
                                <strong>{{ $invoice->bundle->title }}</strong>
                            @elseif($invoice->course)
                                <strong>{{ $invoice->course->title }}</strong>
                            @elseif($invoice->paymentable)
                                <strong>{{ $invoice->paymentable->title ?? ($invoice->paymentable->name ?? 'Finance Mastery') }}</strong>
                            @else
                                <strong>Finance Mastery</strong>
                            @endif
                        </td>
                        <td>1</td>
                        <td>999293</td>
                        <td class="text-right">INR {{ number_format($subTotal, 2) }}</td>
                    </tr>

                    <!-- Sub Total Row -->
                    <tr>
                        <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                        <td class="text-right"><strong>INR {{ number_format($subTotal, 2) }}</strong></td>
                    </tr>

                    <!-- Tax Split Row (Table structure for strict PDF printing compatibility) -->
                    <tr class="tax-row">
                        <td colspan="3" style="padding: 0; background-color: #fafafa; border: 1px solid #eaeaea;">
                            <table style="width: 100%; border-collapse: collapse; margin: 0; border: none;">
                                <tr>
                                    <td style="width: 35%; text-align: right; padding: 12px; border: none; border-right: 1px solid #eaeaea; font-weight: 600; color: #2D2D2D; font-size: 12px;">Tax 18%:</td>
                                    <td style="width: 15%; text-align: center; padding: 12px; border: none; border-right: 1px solid #eaeaea; font-size: 12px; color: #555555;">CGST: 9%</td>
                                    <td style="width: 20%; text-align: center; padding: 12px; border: none; border-right: 1px solid #eaeaea; font-size: 12px; color: #555555;">CGST: INR {{ number_format($cgst, 2) }}</td>
                                    <td style="width: 10%; text-align: center; padding: 12px; border: none; border-right: 1px solid #eaeaea; font-size: 12px; color: #555555;">SGST: 9%</td>
                                    <td style="width: 20%; text-align: center; padding: 12px; border: none; font-size: 12px; color: #555555;">SGST: INR {{ number_format($sgst, 2) }}</td>
                                </tr>
                            </table>
                        </td>
                        <td class="text-center" style="vertical-align: middle; background-color: #fafafa;">
                            <strong>INR {{ number_format($taxAmount, 2) }}</strong>
                        </td>
                    </tr>

                    <!-- Total Row -->
                    <tr>
                        <td colspan="3" class="text-right" style="font-size: 16px;"><strong>Total:</strong></td>
                        <td class="text-right" style="font-size: 16px;">
                            <strong>INR {{ number_format($totalAmount, 2) }}</strong></td>
                    </tr>

                    <!-- Declaration -->
                    <tr class="declaration-row">
                        <td colspan="4">
                            <strong>Declaration: We declare that this invoice shows the actual price of the goods/ services
                                described and that all particulars are true and correct.</strong>
                        </td>
                    </tr>

                    <!-- Computer Generated Note -->
                    <tr class="computer-generated-row">
                        <td colspan="4">
                            This is a Computer Generated Invoice No Signature Required.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Print Action -->
        @if(!isset($is_pdf) || !$is_pdf)
        <div class="bottom-actions" id="action-area">
            @if(isset($downloadUrl))
                <a href="{{ $downloadUrl }}" class="print-btn" style="text-decoration: none; display: inline-block;">Download Invoice (PDF)</a>
            @else
                <button onclick="window.print()" class="print-btn">Print Invoice</button>
            @endif
            <button onclick="window.print()" style="background:none; border:none; text-decoration:underline; cursor:pointer; color:var(--color-text-muted); font-size:12px; margin-top:5px;">Print Instead</button>
            <span>Generated on {{ now()->format('d-m-Y H:i:s') }}</span>
        </div>
        @endif

    </div>

</body>

</html>
