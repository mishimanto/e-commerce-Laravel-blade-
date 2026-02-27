<?php
// app/Http/Controllers/OrderInvoiceController.php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderInvoiceController extends Controller
{
    protected $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Download invoice as PDF
     */
    public function download(Request $request, Order $order)
    {
        try {
            // Check authorization for guest users
            if (!auth()->check()) {
                $lastOrderId = session('last_order_id');
                if (!$lastOrderId || $lastOrderId != $order->id) {
                    abort(403, 'Unauthorized access to this invoice.');
                }
            } elseif (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
                // Logged in user but trying to access someone else's order
                if (!auth()->user()->isAdmin()) {
                    abort(403, 'You are not authorized to view this invoice.');
                }
            }

            // Load order with details
            $order = $this->orderRepository->getOrderWithDetails($order->id);
            
            // Generate PDF
            $pdf = Pdf::loadView('admin.orders.invoice', compact('order'));
            
            // Download PDF
            return $pdf->download("invoice-{$order->order_number}.pdf");

        } catch (\Exception $e) {
            Log::error('Invoice download error: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate invoice. Please try again.');
        }
    }

    /**
     * Print invoice (view for printing)
     */
    public function print(Request $request, Order $order)
    {
        try {
            // Same authorization check
            if (!auth()->check()) {
                $lastOrderId = session('last_order_id');
                if (!$lastOrderId || $lastOrderId != $order->id) {
                    abort(403, 'Unauthorized access to this invoice.');
                }
            } elseif (auth()->check() && $order->user_id && $order->user_id !== auth()->id()) {
                if (!auth()->user()->isAdmin()) {
                    abort(403, 'You are not authorized to view this invoice.');
                }
            }

            // Load order with details
            $order = $this->orderRepository->getOrderWithDetails($order->id);
            
            // Return print view
            return view('admin.orders.print', compact('order'));

        } catch (\Exception $e) {
            Log::error('Invoice print error: ' . $e->getMessage());
            return back()->with('error', 'Failed to load invoice for printing.');
        }
    }
}