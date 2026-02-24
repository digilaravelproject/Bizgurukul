<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->razorpay_order_id ?? $invoice->id }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 40px;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4F46E5;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h2 {
            margin: 0;
            color: #555;
        }

        .invoice-details p {
            margin: 5px 0 0;
            color: #777;
            font-size: 14px;
        }

        .billing-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .billing-to,
        .billing-from {
            width: 45%;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            text-align: left;
            background: #f9fafb;
            padding: 10px;
            border-bottom: 2px solid #eee;
            font-size: 12px;
            text-transform: uppercase;
            color: #555;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .total-row td {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #333;
            border-bottom: none;
        }

        .footer {
            text-align: center;
            color: #999;
            font-size: 12px;
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }

        .print-btn {
            background: #4F46E5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .print-btn:hover {
            background: #4338ca;
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                padding: 0;
            }

            .invoice-box {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body>

    <div style="text-align: center;">
        <button onclick="window.print()" class="print-btn">Print Invoice / Save as PDF</button>
    </div>

    <div class="invoice-box">
        <div class="header">
            <div class="logo">
                @if($settings->company_logo)
                    <img src="{{ asset('storage/' . $settings->company_logo) }}" alt="{{ $settings->site_name }}" style="max-height: 60px;">
                @else
                    {{ $settings->site_name }}
                @endif
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p>#{{ $invoice->razorpay_order_id ?? $invoice->id }}</p>
                <p>Date: {{ $invoice->created_at->format('d M, Y') }}</p>
            </div>
        </div>

        <div class="billing-info">
            <div class="billing-from">
                <h4>From:</h4>
                <p><strong>{{ $settings->site_name }}</strong><br>
                    {{ $settings->company_address }}<br>
                    {{ $settings->company_city }}, {{ $settings->company_state }} - {{ $settings->company_zip }}<br>
                    Email: {{ $settings->company_email }}</p>
            </div>
            <div class="billing-to">
                <h4>Bill To:</h4>
                <p><strong>{{ $invoice->user->name }}</strong><br>
                    {{ $invoice->user->email }}<br>
                    {{ $invoice->user->mobile ?? '' }}</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        @if ($invoice->bundle)
                            <strong>{{ $invoice->bundle->title }}</strong> (Bundle)
                        @elseif($invoice->course)
                            <strong>{{ $invoice->course->title }}</strong> (Course)
                        @elseif($invoice->paymentable)
                            <strong>{{ $invoice->paymentable->title ?? ($invoice->paymentable->name ?? 'Item') }}</strong>
                            ({{ class_basename($invoice->paymentable_type) }})
                        @else
                            Product Purchase
                        @endif
                    </td>
                    <td style="text-align: right;">₹{{ number_format($invoice->subtotal ?? $invoice->amount, 2) }}</td>
                </tr>

                @if($invoice->discount_amount > 0)
                <tr>
                    <td style="text-align: right; color: #059669;">Discount (Coupon):</td>
                    <td style="text-align: right; color: #059669;">- ₹{{ number_format($invoice->discount_amount, 2) }}</td>
                </tr>
                @endif

                @if($invoice->tax_details && is_array($invoice->tax_details))
                    @foreach($invoice->tax_details as $tax)
                    <tr>
                        <td style="text-align: right; color: #6b7280;">
                            {{ $tax['name'] }} ({{ $tax['value'] }}{{ $tax['type'] == 'percentage' ? '%' : '' }} {{ $tax['tax_type'] == 'inclusive' ? 'Incl.' : 'Excl.' }}):
                        </td>
                        <td style="text-align: right; color: #6b7280;">₹{{ number_format($tax['calculated_amount'] ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                @elseif($invoice->tax_amount > 0)
                <tr>
                    <td style="text-align: right; color: #6b7280;">Tax (GST):</td>
                    <td style="text-align: right; color: #6b7280;">₹{{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td style="text-align: right;">Total Paid:</td>
                    <td style="text-align: right;">₹{{ number_format($invoice->total_amount ?? $invoice->amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
        </div>
    </div>

</body>

</html>
