<?php
// app/Notifications/OrderConfirmation.php

namespace App\Notifications;

use App\Models\Order;
use App\Repositories\OrderRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class OrderConfirmation extends Notification 
{
    use Queueable;

    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        try {
            // Generate signed URLs for invoice
            $signedDownloadUrl = URL::temporarySignedRoute(
                'order.invoice.download',
                now()->addDays(7),
                ['order' => $this->order->id]
            );

            $signedPrintUrl = URL::temporarySignedRoute(
                'order.invoice.print',
                now()->addDays(7),
                ['order' => $this->order->id]
            );

            // Calculate totals
            $subtotal = $this->order->items->sum(function($item) {
                return $item->price * $item->quantity;
            });

            // Prepare data for view
            $data = [
                'order' => $this->order,
                'notifiable' => $notifiable,
                'subtotal' => $subtotal,
                'signedDownloadUrl' => $signedDownloadUrl,
                'signedPrintUrl' => $signedPrintUrl
            ];

            // Generate PDF for attachment
            $orderRepository = app(OrderRepository::class);
            $orderWithDetails = $orderRepository->getOrderWithDetails($this->order->id);
            $pdf = Pdf::loadView('admin.orders.invoice', ['order' => $orderWithDetails]);

            // Create email with view
            $mailMessage = (new MailMessage)
                ->subject('Order Confirmation - ' . $this->order->order_number)
                ->view('emails.order-confirmation', $data)  // 👈 view() method ব্যবহার করুন
                ->attachData($pdf->output(), "invoice-{$this->order->order_number}.pdf", [
                    'mime' => 'application/pdf',
                ]);

            return $mailMessage;

        } catch (\Exception $e) {
            Log::error('❌ Error preparing email: ' . $e->getMessage());
            
            // Fallback plain text email
            return (new MailMessage)
                ->subject('Order Confirmation - ' . $this->order->order_number)
                ->greeting('Dear ' . ($this->order->billing_name ?? 'Customer') . '!')
                ->line('Thank you for your order. Order #' . $this->order->order_number)
                ->line('Total: ৳' . number_format($this->order->total, 2))
                ->line('We will notify you when your order ships.')
                ->line('Thank you for shopping with ' . config('app.name') . '!');
        }
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
            'status' => $this->order->status,
            'message' => 'Your order has been placed successfully.'
        ];
    }
}