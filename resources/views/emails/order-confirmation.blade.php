{{-- resources/views/emails/order-confirmation.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2563eb;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .order-details {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border: 1px solid #e5e7eb;
        }
        .invoice-section {
            background-color: #eef2ff;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
            text-align: center;
            border: 2px dashed #2563eb;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            margin: 10px 5px;
            font-weight: 500;
        }
        .button-secondary {
            background-color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th {
            background-color: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Order!</h1>
        </div>
        
        <div class="content">
            <p>Dear {{ $order->billing_name ?? $notifiable->name ?? 'Customer' }},</p>
            
            <p>Your order has been placed successfully. We're now processing it and will notify you when it ships.</p>
            
            <div class="order-details">
                <h3>Order Summary</h3>
                <p><strong>Order Number:</strong> #{{ $order->order_number }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('F j, Y, g:i a') }}</p>
                <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
            </div>
            
            <h3>Order Items</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>৳{{ number_format($item->price, 2) }}</td>
                        <td>৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="text-align: right; margin: 20px 0;">
                <p><strong>Subtotal:</strong> ৳{{ number_format($subtotal, 2) }}</p>
                <p><strong>Shipping:</strong> ৳{{ number_format($order->shipping_cost ?? 0, 2) }}</p>
                <p><strong>Tax:</strong> ৳{{ number_format($order->tax_amount ?? 0, 2) }}</p>
                @if($order->discount_amount > 0)
                <p><strong>Discount:</strong> -৳{{ number_format($order->discount_amount, 2) }}</p>
                @endif
                <p style="font-size: 18px; font-weight: bold; color: #2563eb;"><strong>Total:</strong> ৳{{ number_format($order->total, 2) }}</p>
            </div>
            
            
            <div style="margin: 20px 0;">
                <a href="{{ $signedDownloadUrl }}">Download Invoice </a>
            </div>
                
            <p style="font-size: 13px; color: #4b5563;">
                <small>Note: This link will expire in 7 days. Save it for future reference.</small>
            </p>
            
            <div style="margin-top: 20px; color: white; text-align: center;">
                <a href="{{ route('home') }}">Continue Shopping</a>
            </div>
            
            <div class="footer">
                <p>If you have any questions, please contact with us</p>
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>