<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    protected $orderRepository;
    protected $orderService;

    public function __construct(OrderRepository $orderRepository, OrderService $orderService) {
        $this->orderRepository = $orderRepository;
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $orders = $this->orderRepository->getFilteredOrders($request->all());

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order = $this->orderRepository->getOrderWithDetails($order->id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'field' => 'required|string|in:status,payment_status'
        ]);

        try {
            if ($request->field === 'payment_status') {
                $order->payment_status = $request->status;
                $message = 'Payment status updated successfully.';
            } else {
                $order->status = $request->status;
                $message = 'Order status updated successfully.';
            }
            
            $order->save();

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    public function addTracking(Request $request, Order $order)
    {
        $request->validate([
            'shipping_courier' => 'required|string',
            'tracking_number' => 'required|string'
        ]);

        try {
            $order->shipping_courier = $request->shipping_courier;
            $order->tracking_number = $request->tracking_number;
            $order->save();

            return redirect()->back()->with('success', 'Tracking information added successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add tracking: ' . $e->getMessage());
        }
    }

    public function saveNotes(Request $request, Order $order)
    {
        $request->validate([
            'admin_notes' => 'nullable|string'
        ]);

        try {
            $order->admin_notes = $request->admin_notes;
            $order->save();

            return redirect()->back()->with('success', 'Notes saved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save notes: ' . $e->getMessage());
        }
    }
    public function bulkStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|string'
        ]);

        try {
            Order::whereIn('id', $request->order_ids)
                ->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => count($request->order_ids) . ' orders updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function invoice(Order $order)
    {
        $order = $this->orderRepository->getOrderWithDetails($order->id);

        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
        return $pdf->download("invoice-{$order->order_number}.pdf");
    }

    public function printInvoice(Order $order)
    {
        $order = $this->orderRepository->getOrderWithDetails($order->id);
        return view('admin.orders.print', compact('order'));
    }

    public function export(Request $request)
    {
        $orders = $this->orderRepository->getFilteredOrders($request->all(), 1000);

        $filename = "orders-" . date('Y-m-d') . ".csv";
        $handle = fopen('php://temp', 'w+');

        // Add headers
        fputcsv($handle, [
            'Order #',
            'Date',
            'Customer Name',
            'Customer Email',
            'Total',
            'Status',
            'Payment Status',
            'Payment Method'
        ]);

        // Add data
        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->order_number,
                $order->created_at->format('Y-m-d H:i:s'),
                $order->user->name ?? $order->billing_name,
                $order->user->email ?? $order->billing_email,
                $order->total,
                $order->status,
                $order->payment_status,
                $order->payment_method
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

public function printReport(Request $request)
{
    try {
        $orders = $this->orderRepository->getFilteredOrders($request->all());
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.orders.print-report', compact('orders'))->render()
            ]);
        }
        
        return view('admin.orders.print-report', compact('orders'));
    } catch (\Exception $e) {
        \Log::error('Print Report Error: ' . $e->getMessage());
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
        
        return back()->with('error', 'Failed to generate report: ' . $e->getMessage());
    }
}
}