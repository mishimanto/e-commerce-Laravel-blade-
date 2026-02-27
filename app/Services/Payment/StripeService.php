<?php

namespace App\Services\Payment;

use App\Models\Order;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected $secretKey;
    protected $publicKey;
    protected $webhookSecret;

    public function __construct()
    {
        $this->secretKey = config('payment.stripe.secret_key');
        $this->publicKey = config('payment.stripe.public_key');
        $this->webhookSecret = config('payment.stripe.webhook_secret');
        
        Stripe::setApiKey($this->secretKey);
    }

    /**
     * Create payment intent
     */
    public function createPaymentIntent(Order $order)
    {
        try {
            $amount = $order->total * 100; // Convert to cents/paisa
            
            $intent = PaymentIntent::create([
                'amount' => (int) $amount,
                'currency' => strtolower($order->currency ?? 'bdt'),
                'metadata' => [
                    'order_number' => $order->order_number,
                    'customer_name' => $order->user->name ?? $order->shipping_name,
                    'customer_email' => $order->user->email ?? $order->shipping_email,
                ],
                'description' => 'Order #' . $order->order_number,
            ]);

            return [
                'success' => true,
                'client_secret' => $intent->client_secret,
                'intent_id' => $intent->id,
                'public_key' => $this->publicKey
            ];

        } catch (\Exception $e) {
            Log::error('Stripe create payment intent failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to create payment intent: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Confirm payment intent
     */
    public function confirmPaymentIntent($intentId, $paymentMethodId)
    {
        try {
            $intent = PaymentIntent::retrieve($intentId);
            
            // Attach payment method
            $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['payment_intent' => $intentId]);
            
            // Confirm
            $intent->confirm([
                'payment_method' => $paymentMethodId
            ]);

            if ($intent->status === 'succeeded') {
                return [
                    'success' => true,
                    'transaction_id' => $intent->id,
                    'amount' => $intent->amount / 100,
                    'currency' => strtoupper($intent->currency)
                ];
            }

            return [
                'success' => false,
                'status' => $intent->status,
                'message' => 'Payment requires further action.'
            ];

        } catch (\Exception $e) {
            Log::error('Stripe confirm payment failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment confirmation failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Retrieve payment intent
     */
    public function getPaymentIntent($intentId)
    {
        try {
            $intent = PaymentIntent::retrieve($intentId);
            
            return [
                'success' => true,
                'intent' => $intent
            ];

        } catch (\Exception $e) {
            Log::error('Stripe retrieve intent failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to retrieve payment intent.'
            ];
        }
    }

    /**
     * Cancel payment intent
     */
    public function cancelPaymentIntent($intentId)
    {
        try {
            $intent = PaymentIntent::retrieve($intentId);
            $intent->cancel();

            return [
                'success' => true,
                'message' => 'Payment cancelled successfully.'
            ];

        } catch (\Exception $e) {
            Log::error('Stripe cancel intent failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to cancel payment intent.'
            ];
        }
    }

    /**
     * Handle webhook
     */
    public function handleWebhook($payload, $sigHeader)
    {
        try {
            $event = Webhook::constructEvent(
                $payload, 
                $sigHeader, 
                $this->webhookSecret
            );

            return [
                'success' => true,
                'type' => $event->type,
                'data' => $event->data->object
            ];

        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            throw new \Exception('Invalid payload: ' . $e->getMessage());
            
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            throw new \Exception('Invalid signature: ' . $e->getMessage());
        }
    }

    /**
     * Create refund
     */
    public function refund($paymentIntentId, $amount = null, $reason = null)
    {
        try {
            $params = ['payment_intent' => $paymentIntentId];
            
            if ($amount) {
                $params['amount'] = (int) ($amount * 100);
            }
            
            if ($reason) {
                $params['reason'] = $reason;
            }

            $refund = \Stripe\Refund::create($params);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refund->amount / 100,
                'status' => $refund->status
            ];

        } catch (\Exception $e) {
            Log::error('Stripe refund failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Refund failed: ' . $e->getMessage()
            ];
        }
    }
}