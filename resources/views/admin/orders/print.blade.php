<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        /* আপনার existing print styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', 'Segoe UI', Arial, sans-serif;
            background: #fff;
            color: #333;
            line-height: 1.4;
            padding: 20px;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }

        .company-name {
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 5px;
        }

        .invoice-title {
            font-size: 20px;
            color: #4b5563;
            margin-bottom: 10px;
        }

        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .customer-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .billing-info, .shipping-info {
            width: 48%;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        table th {
            background: #2563eb;
            color: white;
            padding: 10px;
            text-align: left;
        }

        table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .totals {
            margin-top: 30px;
            text-align: right;
        }

        .total-row {
            display: flex;
            justify-content: flex-end;
            margin: 5px 0;
        }

        .total-label {
            width: 150px;
            font-weight: bold;
        }

        .total-value {
            width: 150px;
        }

        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #2563eb;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }

        @media print {
            body {
                padding: 0.5in;
            }
            table th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        .watermark {
            position: fixed;
            bottom: 50px;
            right: 50px;
            opacity: 0.1;
            font-size: 60px;
            font-weight: bold;
            color: #2563eb;
            transform: rotate(-45deg);
            pointer-events: none;
            z-index: -1;
        }
    </style>
</head>
<body>
    <div class="watermark">PHONE GADGETS</div>

    <div class="invoice-header">
        <div class="company-name">Phone Gadgets Store</div>
        <div class="invoice-title">Invoice</div>
        <div>Order #{{ $order->order_number }}</div>
        <div>Date: {{ $order->created_at->format('F d, Y h:i A') }}</div>
    </div>

    <div class="order-info">
        <div>
            <strong>Order Status:</strong> {{ App\Models\Order::STATUSES[$order->status] ?? $order->status }}<br>
            <strong>Payment Status:</strong> {{ App\Models\Order::PAYMENT_STATUSES[$order->payment_status] ?? $order->payment_status }}<br>
            <strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}
        </div>
        <div>
            <strong>Invoice #:</strong> {{ $order->invoice_number ?? $order->order_number }}<br>
            <strong>Invoice Date:</strong> {{ date('F d, Y') }}
        </div>
    </div>

    <div class="customer-info">
        <div class="billing-info">
            <div class="section-title">Billing Address</div>
            <p><strong>{{ $order->billing_name }}</strong></p>
            <p>{{ $order->billing_address }}</p>
            <p>{{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_zip }}</p>
            <p>{{ $order->billing_country }}</p>
            <p>Phone: {{ $order->billing_phone }}</p>
            <p>Email: {{ $order->billing_email }}</p>
        </div>

        <div class="shipping-info">
            <div class="section-title">Shipping Address</div>
            <p><strong>{{ $order->shipping_name }}</strong></p>
            <p>{{ $order->shipping_address }}</p>
            <p>{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_zip }}</p>
            <p>{{ $order->shipping_country }}</p>
            <p>Phone: {{ $order->shipping_phone }}</p>
            <p>Email: {{ $order->shipping_email }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    {{ $item->name }}
                    @if($item->attributes)
                        @php
                            $attributes = is_string($item->attributes) 
                                ? json_decode($item->attributes, true) 
                                : ($item->attributes ?? []);
                        @endphp
                        @if(is_array($attributes) && count($attributes) > 0)
                            <br><small>
                                @foreach($attributes as $key => $value)
                                    {{ ucfirst($key) }}: 
                                    @if(is_array($value))
                                        {{ implode(', ', $value) }}
                                    @else
                                        {{ $value }}
                                    @endif
                                    @if(!$loop->last), @endif
                                @endforeach
                            </small>
                        @endif
                    @endif
                </td>
                <td>{{ $item->sku }}</td>
                <td>৳{{ number_format($item->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>৳{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-value">৳{{ number_format($order->subtotal, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">Shipping:</span>
            <span class="total-value">৳{{ number_format($order->shipping_cost, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">Tax:</span>
            <span class="total-value">৳{{ number_format($order->tax_amount, 2) }}</span>
        </div>
        @if($order->discount_amount > 0)
        <div class="total-row">
            <span class="total-label">Discount:</span>
            <span class="total-value">-৳{{ number_format($order->discount_amount, 2) }}</span>
        </div>
        @endif
        <div class="total-row grand-total">
            <span class="total-label">Total:</span>
            <span class="total-value">৳{{ number_format($order->total, 2) }}</span>
        </div>
    </div>

    @if($order->notes)
    <div style="margin-top: 30px; padding: 15px; background: #f9fafb; border-radius: 5px;">
        <strong>Notes:</strong>
        <p style="margin-top: 5px;">{{ $order->notes }}</p>
    </div>
    @endif

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer generated invoice - no signature required.</p>
    </div>
</body>
</html>