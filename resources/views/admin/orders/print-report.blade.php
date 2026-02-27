<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Report - {{ date('d M, Y') }}</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background: #ffffff;
            color: #1e293b;
            line-height: 1.5;
            padding: 40px;
            font-size: 14px;
        }

        /* Letterhead / Header */
        .letterhead {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }

        .company-info h1 {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.5px;
            margin-bottom: 5px;
        }

        .company-info p {
            color: #475569;
            font-size: 13px;
            margin: 2px 0;
        }

        .report-meta {
            text-align: right;
        }

        .report-meta .title {
            font-size: 20px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .report-meta .date {
            color: #64748b;
            font-size: 13px;
        }

        .report-meta .badge {
            border-radius: 8px;
            font-size: 12px;
            color: #334155;
            display: inline-block;
            margin-top: 8px;
        }

        /* Filter Summary */
        .filter-summary {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .filter-summary h3 {
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-summary h3 svg {
            width: 18px;
            height: 18px;
            color: #64748b;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .filter-label {
            font-size: 12px;
            font-weight: 500;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .filter-value {
            font-size: 14px;
            font-weight: 500;
            color: #0f172a;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .stat-label {
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .stat-sub {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 5px;
        }

        /* Table Styles */
        .table-container {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th {
            background: #f8fafc;
            color: #334155;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafafa;
        }

        /* Status Badges */
        .status-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
            letter-spacing: 0.3px;
            background: #f1f5f9;
            color: #334155;
        }

        /* Order Status Colors */
        .status-pending { background: #fff7ed; color: #9a3412; }
        .status-processing { background: #eff6ff; color: #1d4ed8; }
        .status-confirmed { background: #e0f2fe; color: #0369a1; }
        .status-shipped { background: #f3e8ff; color: #6b21a8; }
        .status-delivered { background: #dcfce7; color: #166534; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-refunded { background: #fff1f2; color: #9f1239; }
        .status-failed { background: #fef2f2; color: #b91c1c; }

        /* Payment Status Colors */
        .status-paid { background: #dcfce7; color: #166534; }
        .status-pending { background: #fff7ed; color: #9a3412; }
        .status-failed { background: #fee2e2; color: #991b1b; }
        .status-refunded { background: #fef3c7; color: #92400e; }

        /* Footer */
        .footer {
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 60px;
            font-size: 14px;
            color: #64748b;
        }

        .signature-area {
            display: flex;
            gap: 60px;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            width: 180px;
            border-top: 1px solid #94a3b8;
            margin-bottom: 8px;
        }

        .signature-label {
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Page Numbers */
        .page-number {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
            color: #94a3b8;
        }

        /* Print Styles */
        @media print {
            body {
                padding: 20px;
                background: white;
            }
            
            .stat-card {
                box-shadow: none;
                border: 1px solid #ddd;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .status-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            th {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: #f5f5f5 !important;
            }
            
            .filter-summary {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                background: #f9f9f9;
            }
            
            tr:hover td {
                background: none;
            }
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.03;
            font-size: 100px;
            font-weight: 800;
            color: #0f172a;
            pointer-events: none;
            z-index: -1;
            letter-spacing: 5px;
        }

        /* Utility Classes */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .text-muted { color: #64748b; }
    </style>
</head>
<body>
    <div class="watermark">GadgetBD</div>

    <!-- Header / Letterhead -->
    <div class="letterhead">
        <div class="company-info">
            <h1>GadgetBD</h1>
            <p>123 Main Street, Dhaka - 1000, Bangladesh</p>
            <p>+880 1234-567890 | info@gadgetbd.com</p>
        </div>
        <div class="report-meta">
            <div class="title">Orders Report</div>
            <div class="date">{{ date('F d, Y h:i A') }}</div>
            <span class="badge">Reference: OR/{{ date('Ymd') }}</span>
        </div>
    </div>

    <!-- Filter Summary -->
    @if(request()->anyFilled(['search', 'status', 'payment_status', 'date_from', 'date_to']))
    <div class="filter-summary">
        <h3>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
            </svg>
            Applied Filters
        </h3>
        <div class="filter-grid">
            @if(request('search'))
            <div class="filter-item">
                <span class="filter-label">Search</span>
                <span class="filter-value">{{ request('search') }}</span>
            </div>
            @endif

            @if(request('status'))
            <div class="filter-item">
                <span class="filter-label">Order Status</span>
                <span class="filter-value">{{ App\Models\Order::STATUSES[request('status')] ?? request('status') }}</span>
            </div>
            @endif

            @if(request('payment_status'))
            <div class="filter-item">
                <span class="filter-label">Payment Status</span>
                <span class="filter-value">{{ App\Models\Order::PAYMENT_STATUSES[request('payment_status')] ?? request('payment_status') }}</span>
            </div>
            @endif

            @if(request('date_from'))
            <div class="filter-item">
                <span class="filter-label">From</span>
                <span class="filter-value">{{ \Carbon\Carbon::parse(request('date_from'))->format('M d, Y') }}</span>
            </div>
            @endif

            @if(request('date_to'))
            <div class="filter-item">
                <span class="filter-label">To</span>
                <span class="filter-value">{{ \Carbon\Carbon::parse(request('date_to'))->format('M d, Y') }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $orders->count() }}</div>
            <div class="stat-sub">All time</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">৳{{ number_format($orders->sum('total'), 0) }}</div>
            <div class="stat-sub">Net sales</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Pending Orders</div>
            <div class="stat-value">{{ $orders->where('status', 'pending')->count() }}</div>
            <div class="stat-sub">Awaiting processing</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Completed</div>
            <div class="stat-value">{{ $orders->whereIn('status', ['delivered', 'completed'])->count() }}</div>
            <div class="stat-sub">Successfully delivered</div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date & Time</th>
                    <th>Customer</th>
                    <th class="text-center">Items</th>
                    <th class="text-right">Total</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Payment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="font-medium">{{ $order->order_number }}</td>
                    <td>
                        {{ $order->created_at->format('M d, Y') }}<br>
                        <span class="text-muted">{{ $order->created_at->format('h:i A') }}</span>
                    </td>
                    <td>
                        <div class="font-medium">{{ $order->user->name ?? $order->billing_name }}</div>
                        <div class="text-muted">{{ $order->user->email ?? $order->billing_email }}</div>
                    </td>
                    <td class="text-center font-medium">{{ $order->items->sum('quantity') }}</td>
                    <td class="text-right font-semibold">৳{{ number_format($order->total, 2) }}</td>
                    <td>
                        <span class="status-badge status-{{ $order->status }}">
                            {{ App\Models\Order::STATUSES[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $order->payment_status }}">
                            {{ App\Models\Order::PAYMENT_STATUSES[$order->payment_status] ?? ucfirst($order->payment_status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 60px 20px;">
                        <div style="font-size: 14px; color: #64748b;">No orders found</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div>
            <!-- <span class="font-medium">{{ $orders->count() }}</span> orders found •  -->
            Generated on <span class="font-medium">{{ date('Y-m-d H:i:s') }}</span>
        </div>
    </div>

    <!-- Page Number -->
    <!-- <div class="page-number">
        Page 1 of 1 
    </div> -->
</body>
</html>