<?php
// app/Services/Payment/PaymentService.php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function processPayment(Order $order, string $method, array $data = [])
    {
        try {
            // For demo, just log and return success
            Log::info('Payment processed', [
                'order_id' => $order->id,
                'method' => $method,
                'amount' => $order->total
            ]);

            return [
                'success' => true,
                'message' => 'Payment processed successfully',
                'transaction_id' => 'TXN-' . strtoupper(uniqid())
            ];

        } catch (\Exception $e) {
            Log::error('Payment failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ];
        }
    }

    public function processCashOnDelivery(Order $order)
    {
        return $this->processPayment($order, 'cash_on_delivery');
    }

    public function processBkash(Order $order, array $data)
    {
        return $this->processPayment($order, 'bkash', $data);
    }

    public function processNagad(Order $order, array $data)
    {
        return $this->processPayment($order, 'nagad', $data);
    }

    public function processRocket(Order $order, array $data)
    {
        return $this->processPayment($order, 'rocket', $data);
    }

    public function verifyPayment($transactionId)
    {
        // For demo, always return success
        return [
            'success' => true,
            'data' => [
                'transaction_id' => $transactionId,
                'status' => 'completed'
            ]
        ];
    }
}