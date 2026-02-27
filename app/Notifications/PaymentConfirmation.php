<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmation extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Payment Confirmed - ' . $this->order->order_number)
            ->greeting('Dear ' . $this->order->billing_name . '!')
            ->line('Your payment has been confirmed for order ' . $this->order->order_number)
            ->line('Payment Method: ' . ucfirst(str_replace('_', ' ', $this->order->payment_method)))
            ->line('Amount Paid: ৳' . number_format($this->order->total, 2))
            ->line('We will process your order soon.')
            ->action('View Order', route('order.track', $this->order->order_number));
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->order->total,
            'message' => 'Payment confirmed for order'
        ];
    }
}