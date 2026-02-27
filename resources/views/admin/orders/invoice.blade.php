<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice #{{ $order->order_number }}</title>

<style>
@page { margin: 0; }

body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    color: #333;
    margin: 0;
}

.wrapper {
    padding: 40px 50px;
}

/* Top Address */
.top-address {
    font-size: 11px;
    color: #777;
    line-height: 1.5;
    text-align: right;
}

/* Invoice Title */
.invoice-title {
    font-size: 38px;
    font-weight: bold;
    color: #d4d4d4;
    margin: 0 0 20px 0;
}

/* Invoice Meta */
.meta-table {
    width: 100%;
    margin-bottom: 40px;
}

.meta-table td {
    padding: 3px 0;
    font-size: 11px;
}

/* Description */
.invoice-desc {
    margin: 15px 0;
    font-size: 11px;
}

/* Table */
.invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px; 
    margin-bottom: 20px;
}

.invoice-table th {
    background: #dcdcdc;
    padding: 8px;
    border: 1px solid #cfcfcf;
    font-size: 11px;
    text-align: left;
}

.invoice-table td {
    padding: 8px;
    border: 1px solid #e5e5e5;
    font-size: 11px;
    vertical-align: top;
}

.text-center { text-align: center; }
.text-right { text-align: right; }

/* Totals */
.totals-row td {
    background: #f3f3f3;
    font-weight: bold;
}

/* Bottom Sections */
.bottom-wrapper {
    margin-top: 30px;
    width: 100%;
}

.bottom-left {
    width: 55%;
    float: left;
    font-size: 11px;
    line-height: 1.6;
}

.bottom-right {
    width: 40%;
    float: right;
    font-size: 11px;
    line-height: 1.6;
}

.footer {
    position: fixed;
    bottom: 20px;
    width: 100%;
    text-align: center;
    font-size: 9px;
    color: #aaa;
}

.clear { clear: both; }
</style>
</head>

<body>
<div class="wrapper">

    <!-- Company Address -->
    <div class="top-address">
        <span style="font-size: 14px; font-weight: bold; color: #000">{{ config('settings.store_name') }}</span><br>
        {{ config('settings.store_address') }}<br>
        {{ config('settings.store_phone') }}<br>
        {{ config('settings.store_email') }}
    </div>

    <!-- Title -->
    <div class="invoice-title">INVOICE</div>

    <!-- Invoice Meta -->
    <table class="meta-table">
        <tr>
            <td width="15%"><strong>Invoice</strong></td>
            <td width="35%">#{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td><strong>Date</strong></td>
            <td>{{ $order->created_at->format('d/m/Y') }}</td>
        </tr>
        <!-- <tr>
            <td><strong>Due Date</strong></td>
            <td>{{ $order->created_at->addDays(7)->format('d/m/Y') }}</td>
        </tr> -->
        @foreach($order->items as $item)
        <tr>
            <td><strong>Warranty</strong></td>
            <td style="color: green; font-weight: bold;">{{  $item->product->warranty ?? 'No Warranty' }} </td>
        </tr>
        @endforeach
    </table>

    <div class="invoice-desc">
        Thank you for your purchase. Below are the details of your order.
    </div>

    <!-- Items -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th width="45%">Description</th>
                <th width="15%" class="text-center" style="text-align: center">Quantity</th>
                <th width="20%" class="text-center" style="text-align: center">Price</th>
                <th width="20%" class="text-right" style="text-align: center">Total</th>
            </tr>
        </thead>
        <tbody>

        @foreach($order->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->name }}</strong><br>

                    @php
                        $attributes = is_array($item->attributes)
                            ? $item->attributes
                            : json_decode($item->attributes, true);
                    @endphp

                    @if(!empty($attributes) && is_array($attributes))
                        @foreach($attributes as $key => $value)
                            <strong>{{ ucfirst($key) }}:</strong> {{ $value }}<br>
                        @endforeach
                    @endif
                </td>

                <td class="text-center">
                    {{ $item->quantity }}
                </td>

                <td class="text-center">
                    BDT {{ number_format($item->price, 2) }}
                </td>

                <td class="text-right">
                    BDT {{ number_format($item->subtotal, 2) }}
                </td>
            </tr>
        @endforeach

            <!-- Subtotal -->
            <!-- <tr class="totals-row">
                <td colspan="3" class="text-right">Subtotal</td>
                <td class="text-right">
                    BDT {{ number_format($order->sum('subtotal'), 2) }}
                </td>
            </tr> -->

            <!-- Shipping -->
            <tr>
                <td colspan="3" class="text-right">Shipping</td>
                <td class="text-right">
                    BDT {{ number_format($order->shipping_cost, 2) }}
                </td>
            </tr>

            <!-- VAT -->
            <tr>
                <td colspan="3" class="text-right">VAT</td>
                <td class="text-right">
                    BDT {{ number_format($order->tax_amount, 2) }}
                </td>
            </tr>

            @if($order->discount_amount > 0)
            <tr>
                <td colspan="3" class="text-right">Discount</td>
                <td class="text-right">
                    -BDT {{ number_format($order->discount_amount, 2) }}
                </td>
            </tr>
            @endif

            <!-- Grand Total -->
            <tr class="totals-row">
                <td colspan="3" class="text-right">Total BDT</td>
                <td class="text-right">
                    BDT {{ number_format($order->total, 2) }}
                </td>
            </tr>

        </tbody>
    </table>

    <!-- Bottom Section -->
    <div class="bottom-wrapper">
        <div class="bottom-left">
            <span style="font-weight: bold; font-size: 12px; color: #000">Billing Address: </span><br>
            {{ $order->billing_name }}<br>
            {{ $order->billing_address }}<br>
            {{ $order->billing_city }}, {{ $order->billing_state }}<br>
            Phone: {{ $order->billing_phone }}
        </div>

        <div class="bottom-right">
             <span style="font-weight: bold; font-size: 12px; color: #000">Shipping Address: </span><br>
            {{ $order->shipping_name }}<br>
            {{ $order->shipping_address }}<br>
            Method: {{ strtoupper($order->shipping_method) }}<br>
            Phone: {{ $order->shipping_phone }}
        </div>

        <div class="clear"></div>
    </div>

    <!-- Footer -->
    <div class="footer">
        Thank for stay with us. Happy Shopping! 
    </div>

</div>
</body>
</html>