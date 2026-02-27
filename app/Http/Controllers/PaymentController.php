<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Process payment for order
     */
    public function process(Order $order, Request $request)
    {
        try {
            $paymentMethod = $request->get('payment_method', $order->payment_method);

            $result = $this->paymentService->processPayment($order, $paymentMethod);

            if ($result['success']) {
                if ($result['redirect']) {
                    return redirect($result['redirect_url']);
                }

                return redirect()->route('checkout.success', $order)
                    ->with('success', 'Payment completed successfully.');
            }

            return redirect()->route('checkout.cancel', $order)
                ->with('error', $result['message']);

        } catch (\Exception $e) {
            Log::error('Payment processing failed: ' . $e->getMessage());
            
            return redirect()->route('checkout.cancel', $order)
                ->with('error', 'Payment processing failed. Please try again.');
        }
    }

    /**
     * SSLCommerz success callback
     */
    public function sslCommerzSuccess(Request $request)
    {
        try {
            $data = $request->all();
            
            // Verify transaction
            $verification = $this->paymentService->verifyPayment('sslcommerz', $data);

            if ($verification['success']) {
                $order = Order::where('order_number', $data['tran_id'])->first();

                if ($order) {
                    DB::transaction(function () use ($order, $data) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing'
                        ]);

                        Payment::create([
                            'order_id' => $order->id,
                            'user_id' => $order->user_id,
                            'payment_method' => 'sslcommerz',
                            'transaction_id' => $data['bank_tran_id'],
                            'amount' => $data['amount'],
                            'currency' => 'BDT',
                            'status' => 'completed',
                            'payment_data' => $data,
                            'paid_at' => now()
                        ]);
                    });

                    return redirect()->route('checkout.success', $order)
                        ->with('success', 'Payment completed successfully.');
                }
            }

            return redirect()->route('checkout.cancel')
                ->with('error', 'Payment verification failed.');

        } catch (\Exception $e) {
            Log::error('SSLCommerz success callback failed: ' . $e->getMessage());
            
            return redirect()->route('checkout.cancel')
                ->with('error', 'Payment processing failed.');
        }
    }

    /**
     * SSLCommerz fail callback
     */
    public function sslCommerzFail(Request $request)
    {
        $data = $request->all();
        
        Log::warning('SSLCommerz payment failed', $data);

        return redirect()->route('checkout.cancel')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * SSLCommerz cancel callback
     */
    public function sslCommerzCancel(Request $request)
    {
        return redirect()->route('checkout.cancel')
            ->with('warning', 'Payment cancelled.');
    }

    /**
     * SSLCommerz IPN (Instant Payment Notification)
     */
    public function sslCommerzIpn(Request $request)
    {
        try {
            $data = $request->all();
            
            Log::info('SSLCommerz IPN received', $data);

            $order = Order::where('order_number', $data['tran_id'])->first();

            if ($order && $order->payment_status !== 'paid') {
                $verification = $this->paymentService->verifyPayment('sslcommerz', $data);

                if ($verification['success']) {
                    DB::transaction(function () use ($order, $data) {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing'
                        ]);

                        Payment::updateOrCreate(
                            ['order_id' => $order->id],
                            [
                                'user_id' => $order->user_id,
                                'payment_method' => 'sslcommerz',
                                'transaction_id' => $data['bank_tran_id'],
                                'amount' => $data['amount'],
                                'currency' => 'BDT',
                                'status' => 'completed',
                                'payment_data' => $data,
                                'paid_at' => now()
                            ]
                        );
                    });
                }
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('SSLCommerz IPN failed: ' . $e->getMessage());
            
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Stripe webhook handler
     */
    public function stripeWebhook(Request $request)
    {
        try {
            $payload = $request->getContent();
            $sigHeader = $request->header('Stripe-Signature');

            $event = $this->paymentService->handleWebhook('stripe', $payload, $sigHeader);

            switch ($event['type']) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event['data']['object'];
                    
                    $order = Order::where('payment_id', $paymentIntent['id'])->first();

                    if ($order) {
                        DB::transaction(function () use ($order, $paymentIntent) {
                            $order->update([
                                'payment_status' => 'paid',
                                'status' => 'processing'
                            ]);

                            Payment::create([
                                'order_id' => $order->id,
                                'user_id' => $order->user_id,
                                'payment_method' => 'stripe',
                                'transaction_id' => $paymentIntent['id'],
                                'amount' => $paymentIntent['amount'] / 100,
                                'currency' => strtoupper($paymentIntent['currency']),
                                'status' => 'completed',
                                'payment_data' => $paymentIntent,
                                'paid_at' => now()
                            ]);
                        });
                    }
                    break;

                case 'payment_intent.payment_failed':
                    // Handle failed payment
                    break;
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Stripe webhook failed: ' . $e->getMessage());
            
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * bKash payment callback
     */
    public function bkashCallback(Request $request)
    {
        $data = $request->all();
        
        if ($data['status'] === 'success') {
            $order = Order::where('order_number', $data['order_id'])->first();

            if ($order) {
                DB::transaction(function () use ($order, $data) {
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing'
                    ]);

                    Payment::create([
                        'order_id' => $order->id,
                        'user_id' => $order->user_id,
                        'payment_method' => 'bkash',
                        'transaction_id' => $data['trxID'],
                        'amount' => $data['amount'],
                        'currency' => 'BDT',
                        'status' => 'completed',
                        'payment_data' => $data,
                        'paid_at' => now()
                    ]);
                });

                return redirect()->route('checkout.success', $order)
                    ->with('success', 'Payment completed successfully.');
            }
        }

        return redirect()->route('checkout.cancel')
            ->with('error', 'Payment failed. Please try again.');
    }

    /**
     * Nagad payment callback
     */
    public function nagadCallback(Request $request)
    {
        // Similar to bKash callback
        // Implement Nagad specific logic
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(Order $order)
    {
        return response()->json([
            'success' => true,
            'payment_status' => $order->payment_status,
            'order_status' => $order->status
        ]);
    }

    /**
     * Retry failed payment
     */
    public function retryPayment(Order $order)
    {
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order)
                ->with('info', 'Payment already completed.');
        }

        return view('storefront.payment.retry', compact('order'));
    }
}