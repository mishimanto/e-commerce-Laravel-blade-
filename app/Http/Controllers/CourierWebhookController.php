<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CourierWebhookController extends Controller
{
    /**
     * Pathao webhook handler
     */
    public function pathao(Request $request)
    {
        try {
            $data = $request->all();
            
            Log::info('Pathao webhook received', $data);

            $trackingId = $data['consignment_id'] ?? $data['tracking_id'] ?? null;
            
            if ($trackingId) {
                $order = Order::where('tracking_number', $trackingId)->first();

                if ($order) {
                    $status = $this->mapPathaoStatus($data['status'] ?? '');
                    
                    DB::transaction(function () use ($order, $status, $data) {
                        $order->update([
                            'status' => $status,
                            'shipping_data' => array_merge(
                                $order->shipping_data ?? [],
                                ['pathao_webhook' => $data]
                            )
                        ]);

                        // Add timeline entry
                        $order->timeline()->create([
                            'status' => $status,
                            'description' => 'Courier update: ' . ($data['message'] ?? 'Status changed'),
                            'data' => $data
                        ]);
                    });

                    return response()->json(['status' => 'success']);
                }
            }

            return response()->json(['status' => 'ignored']);

        } catch (\Exception $e) {
            Log::error('Pathao webhook failed: ' . $e->getMessage());
            
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Steadfast webhook handler
     */
    public function steadfast(Request $request)
    {
        try {
            $data = $request->all();
            
            Log::info('Steadfast webhook received', $data);

            $trackingId = $data['tracking_code'] ?? $data['consignment_id'] ?? null;
            
            if ($trackingId) {
                $order = Order::where('tracking_number', $trackingId)->first();

                if ($order) {
                    $status = $this->mapSteadfastStatus($data['status'] ?? '');
                    
                    DB::transaction(function () use ($order, $status, $data) {
                        $order->update([
                            'status' => $status,
                            'shipping_data' => array_merge(
                                $order->shipping_data ?? [],
                                ['steadfast_webhook' => $data]
                            )
                        ]);

                        $order->timeline()->create([
                            'status' => $status,
                            'description' => 'Courier update: ' . ($data['status_message'] ?? 'Status changed'),
                            'data' => $data
                        ]);
                    });

                    return response()->json(['status' => 'success']);
                }
            }

            return response()->json(['status' => 'ignored']);

        } catch (\Exception $e) {
            Log::error('Steadfast webhook failed: ' . $e->getMessage());
            
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * RedX webhook handler
     */
    public function redx(Request $request)
    {
        try {
            $data = $request->all();
            
            Log::info('RedX webhook received', $data);

            $trackingId = $data['tracking_id'] ?? $data['parcel_id'] ?? null;
            
            if ($trackingId) {
                $order = Order::where('tracking_number', $trackingId)->first();

                if ($order) {
                    $status = $this->mapRedxStatus($data['parcel_status'] ?? '');
                    
                    DB::transaction(function () use ($order, $status, $data) {
                        $order->update([
                            'status' => $status,
                            'shipping_data' => array_merge(
                                $order->shipping_data ?? [],
                                ['redx_webhook' => $data]
                            )
                        ]);

                        $order->timeline()->create([
                            'status' => $status,
                            'description' => 'Courier update: ' . ($data['note'] ?? 'Status changed'),
                            'data' => $data
                        ]);
                    });

                    return response()->json(['status' => 'success']);
                }
            }

            return response()->json(['status' => 'ignored']);

        } catch (\Exception $e) {
            Log::error('RedX webhook failed: ' . $e->getMessage());
            
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Verify webhook signature
     */
    protected function verifySignature($payload, $signature, $secret)
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Map Pathao status to order status
     */
    protected function mapPathaoStatus($status)
    {
        return match(strtolower($status)) {
            'delivered' => 'delivered',
            'out_for_delivery' => 'shipped',
            'in_transit' => 'shipped',
            'pickup_successful' => 'processing',
            'pickup_failed' => 'cancelled',
            default => 'processing'
        };
    }

    /**
     * Map Steadfast status to order status
     */
    protected function mapSteadfastStatus($status)
    {
        return match(strtolower($status)) {
            'delivered' => 'delivered',
            'out_for_delivery' => 'shipped',
            'in_transit' => 'shipped',
            'pending' => 'processing',
            'cancelled' => 'cancelled',
            default => 'processing'
        };
    }

    /**
     * Map RedX status to order status
     */
    protected function mapRedxStatus($status)
    {
        return match(strtolower($status)) {
            'delivered' => 'delivered',
            'out-for-delivery' => 'shipped',
            'in-transit' => 'shipped',
            'pickup-complete' => 'processing',
            'cancelled' => 'cancelled',
            default => 'processing'
        };
    }
}