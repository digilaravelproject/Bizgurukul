<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->razorpay_order_id ?? $invoice->id }}</title>

    <!-- Importing your custom fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

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
            font-family: 'Outfit', sans-serif;
            color: var(--color-text-main);
            line-height: 1.5;
            padding: 40px 20px;
            background-color: var(--color-bg-body);
            font-size: 14px;
            -webkit-font-smoothing: antialiased;
        }

        .invoice-wrapper {
            max-width: 900px;
            margin: auto;
            background: var(--color-bg-card);
            padding: 40px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border-top: 6px solid var(--color-primary);
            /* Professional accent color */
            border-radius: 4px;
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
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }

            .invoice-wrapper {
                box-shadow: none;
                border-top: 4px solid var(--color-text-main);
                /* Fallback for print */
                margin: 0;
                padding: 0;
                max-width: 100%;
            }

            .bottom-actions {
                display: none;
            }

            /* Ensure background colors print properly */
            th,
            .invoice-meta-box,
            .tax-row td,
            .declaration-row td,
            .computer-generated-row td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
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
        <div class="header-top">
            <div class="logo-container">
                @if (isset($settings->company_logo) && $settings->company_logo)
                    <img src="{{ asset('storage/' . $settings->company_logo) }}"
                        alt="{{ $settings->site_name ?? 'Logo' }}">
                @else
                    <!-- Fallback if no logo -->
                    <h1>{{ $settings->site_name ?? 'SKILLS PEHLE' }}</h1>
                @endif
            </div>
            <div class="company-details">
                <strong>{{ strtoupper($settings->site_name ?? 'SKILLS PEHLE PRIVATE LIMITED') }}</strong><br>
                Regd. Add: {{ $settings->company_address ?? '123 Business Park, Tech Hub' }}<br>
                {{ $settings->company_city ?? 'Mumbai' }}<br>
                {{ $settings->company_state ?? 'Maharashtra' }}, India - {{ $settings->company_zip ?? '400001' }}<br>
                GSTIN: {{ $settings->company_gstin ?? '27HCHPS9578D1ZS' }}<br>
                State Name: {{ $settings->company_state ?? 'Maharashtra' }}, Code : 27<br>
                PAN: {{ $settings->company_pan ?? 'HCHPS9578D' }}
            </div>
        </div>

        <!-- Invoice Meta Data -->
        <div class="invoice-meta-box">
            <div><strong>Invoice#:</strong> {{ $invoice->razorpay_order_id ?? $invoice->id }}</div>
            <div><strong>Invoice Date:</strong> {{ $invoice->created_at->format('d-m-Y') }}</div>
            <div>
                <strong>Status:</strong>
                <span class="status-badge">{{ $displayStatus }}</span>
            </div>
        </div>

        <!-- Billed To -->
        <div class="billed-to">
            <h4>Invoiced To</h4>
            <strong>{{ $invoice->user->name ?? 'Customer Name' }}</strong><br>
            {{ $invoice->user->email }}<br>
            {{ $invoice->user->mobile ?? '' }}
        </div>

        <!-- Items Table -->
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
                    <td class="text-right">₹{{ number_format($subTotal, 2) }}</td>
                </tr>

                <!-- Sub Total Row -->
                <tr>
                    <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                    <td class="text-right"><strong>₹{{ number_format($subTotal, 2) }}</strong></td>
                </tr>

                <!-- Tax Split Row (Exactly as per image layout) -->
                <tr class="tax-row">
                    <td colspan="3">
                        <div class="tax-flex-container">
                            <div class="tax-col tax-label-col">Tax 18%:</div>
                            <div class="tax-col">CGST: 9%</div>
                            <div class="tax-col">CGST Amt: ₹{{ number_format($cgst, 2) }}</div>
                            <div class="tax-col">SGST: 9%</div>
                            <div class="tax-col">SGST Amt: ₹{{ number_format($sgst, 2) }}</div>
                        </div>
                    </td>
                    <td class="text-right" style="vertical-align: middle;">
                        <strong>₹{{ number_format($taxAmount, 2) }}</strong>
                    </td>
                </tr>

                <!-- Total Row -->
                <tr>
                    <td colspan="3" class="text-right" style="font-size: 16px;"><strong>Total:</strong></td>
                    <td class="text-right" style="font-size: 16px;">
                        <strong>₹{{ number_format($totalAmount, 2) }}</strong></td>
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

        <!-- Print Action -->
        <div class="bottom-actions">
            <button onclick="window.print()" class="print-btn">Download/Print Invoice</button>
            <span>Generated on {{ now()->format('d-m-Y H:i:s') }}</span>
        </div>

    </div>

</body>

</html>
