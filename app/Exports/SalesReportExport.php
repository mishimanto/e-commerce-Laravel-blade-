<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Order::with(['user', 'items.product'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Date',
            'Customer Name',
            'Customer Email',
            'Order Total',
            'Payment Method',
            'Order Status',
            'Items Count',
            'Coupon Applied',
            'Discount Amount',
            'Shipping Cost',
            'Net Total'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('Y-m-d H:i:s'),
            $order->user->name ?? 'Guest',
            $order->user->email ?? $order->guest_email,
            $order->total,
            ucfirst(str_replace('_', ' ', $order->payment_method)),
            ucfirst($order->status),
            $order->items->sum('quantity'),
            $order->coupon_code ?? 'N/A',
            $order->discount_amount ?? 0,
            $order->shipping_cost ?? 0,
            $order->total - ($order->discount_amount ?? 0)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}