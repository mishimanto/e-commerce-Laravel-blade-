<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SSLCommerzService
{
    protected $storeId;
    protected $storePassword;
    protected $isSandbox;
    protected $apiUrl;

    public function __construct()
    {
        $this->storeId = config('payment.sslcommerz.store_id');
        $this->storePassword = config('payment.sslcommerz.store_password');
        $this->isSandbox = config('payment.sslcommerz.sandbox', true);
        $this->apiUrl = $this->isSandbox 
            ? 'https://sandbox.sslcommerz.com' 
            : 'https://secure.sslcommerz.com';
    }

    /**
     * Initialize payment session
     */
    public function initiatePayment(Order $order, $successUrl, $failUrl, $cancelUrl)
    {
        $postData = [
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'total_amount' => $order->total,
            'currency' => 'BDT',
            'tran_id' => $order->order_number,
            'success_url' => $successUrl,
            'fail_url' => $failUrl,
            'cancel_url' => $cancelUrl,
            'ipn_url' => route('payment.sslcommerz.ipn'),
            'cus_name' => $order->user->name ?? $order->shipping_name,
            'cus_email' => $order->user->email ?? $order->shipping_email,
            'cus_phone' => $order->user->phone ?? $order->shipping_phone,
            'cus_add1' => $order->shipping_address,
            'cus_city' => $order->shipping_city,
            'cus_state' => $order->shipping_state,
            'cus_postcode' => $order->shipping_zip,
            'cus_country' => $order->shipping_country,
            'ship_name' => $order->shipping_name,
            'ship_add1' => $order->shipping_address,
            'ship_city' => $order->shipping_city,
            'ship_state' => $order->shipping_state,
            'ship_postcode' => $order->shipping_zip,
            'ship_country' => $order->shipping_country,
            'product_name' => 'Order #' . $order->order_number,
            'product_category' => 'Mixed',
            'product_profile' => 'general',
            'shipping_method' => $order->shipping_method ?? 'NO',
            'num_of_item' => $order->items->sum('quantity'),
        ];

        try {
            $response = Http::asForm()->post($this->apiUrl . '/gwprocess/v4/api.php', $postData);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'SUCCESS') {
                    return [
                        'success' => true,
                        'redirect_url' => $data['GatewayPageURL'],
                        'session_key' => $data['sessionkey']
                    ];
                }
            }

            Log::error('SSLCommerz initiation failed', ['response' => $response->body()]);
            
            return [
                'success' => false,
                'message' => 'Failed to initialize payment gateway.'
            ];

        } catch (\Exception $e) {
            Log::error('SSLCommerz exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Payment gateway error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate payment transaction
     */
    public function validatePayment($transactionId, $amount, $currency = 'BDT')
    {
        $validationUrl = $this->isSandbox 
            ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
            : 'https://secure.sslcommerz.com/validator/api/validationserverAPI.php';

        try {
            $response = Http::get($validationUrl, [
                'val_id' => $transactionId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Verify amount and currency
                if ($data['status'] === 'VALID' && 
                    $data['amount'] == $amount && 
                    $data['currency'] == $currency) {
                    
                    return [
                        'success' => true,
                        'data' => $data
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Payment validation failed.'
            ];

        } catch (\Exception $e) {
            Log::error('SSLCommerz validation exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Query transaction status
     */
    public function queryTransaction($transactionId)
    {
        $queryUrl = $this->isSandbox
            ? 'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php'
            : 'https://secure.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php';

        try {
            $response = Http::get($queryUrl, [
                'merchant_trans_id' => $transactionId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'format' => 'json'
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Transaction query failed.'
            ];

        } catch (\Exception $e) {
            Log::error('SSLCommerz query exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Query error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Initiate refund
     */
    public function refund($bankTransactionId, $amount, $reason = '')
    {
        $refundUrl = $this->isSandbox
            ? 'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php'
            : 'https://secure.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php';

        try {
            $response = Http::post($refundUrl, [
                'bank_tran_id' => $bankTransactionId,
                'store_id' => $this->storeId,
                'store_passwd' => $this->storePassword,
                'refund_amount' => $amount,
                'refund_remarks' => $reason,
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['status'] === 'success') {
                    return [
                        'success' => true,
                        'refund_id' => $data['refund_ref_id']
                    ];
                }
            }

            return [
                'success' => false,
                'message' => 'Refund failed.'
            ];

        } catch (\Exception $e) {
            Log::error('SSLCommerz refund exception: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Refund error: ' . $e->getMessage()
            ];
        }
    }
}