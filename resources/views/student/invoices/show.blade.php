<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_no }}</title>

    <!-- Importing your custom fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

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
        @page {
            size: A4;
            margin: 15mm;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
                margin: 0;
                font-size: 12px; /* Slightly smaller font for better fit */
            }

            .invoice-wrapper {
                box-shadow: none;
                border-top: 4px solid var(--color-text-main);
                margin: 0;
                padding: 0;
                max-width: 100%;
                width: 100%;
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
            <div><strong>Invoice Number:</strong> {{ $invoice->invoice_no }}</div>
            <div><strong>Order ID:</strong> {{ $invoice->razorpay_order_id ?? $invoice->id }}</div>
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
                        <td class="text-center" style="vertical-align: middle;">
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
        </div>

        <!-- Print Action -->
        <div class="bottom-actions" id="action-area">
            <button onclick="downloadAsPDF()" class="print-btn" id="dl-btn">Download Invoice (PDF)</button>
            <button onclick="window.print()" style="background:none; border:none; text-decoration:underline; cursor:pointer; color:var(--color-text-muted); font-size:12px; margin-top:5px;">Print Instead</button>
            <span>Generated on {{ now()->format('d-m-Y H:i:s') }}</span>
        </div>

    </div>

    <!-- html2pdf Library for direct PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        function downloadAsPDF() {
            const element = document.querySelector('.invoice-wrapper');
            const actionArea = document.getElementById('action-area');
            const btn = document.getElementById('dl-btn');
            const originalText = btn.innerHTML;

            // Update button state
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;
            btn.style.opacity = '0.7';

            // Temporarily hide the action buttons from being included in the PDF
            actionArea.style.opacity = '0';

            const opt = {
                margin: [10, 5, 10, 5], // top, left, bottom, right
                filename: 'Invoice-{{ str_replace("#", "", $invoice->invoice_no) }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    letterRendering: true,
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            // Run generation
            html2pdf().set(opt).from(element).save().then(() => {
                // Restore UI
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.style.opacity = '1';
                actionArea.style.opacity = '1';
            }).catch(err => {
                console.error('PDF generation error:', err);
                // Restoration on error
                btn.innerHTML = originalText;
                btn.disabled = false;
                btn.style.opacity = '1';
                actionArea.style.opacity = '1';
                // Fallback to print dialog
                window.print();
            });
        }
    </script>

</body>

</html>
