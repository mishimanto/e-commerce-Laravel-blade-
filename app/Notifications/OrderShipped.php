<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderShipped extends Notification implements ShouldQueue
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
            ->subject('Your Order Has Been Shipped - ' . $this->order->order_number)
            ->greeting('Dear ' . $this->order->billing_name . '!')
            ->line('Great news! Your order has been shipped.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Shipping Courier: ' . ($this->order->shipping_courier ?? 'Standard Courier'))
            ->line('Tracking Number: ' . ($this->order->tracking_number ?? 'N/A'))
            ->action('Track Your Order', route('order.track', $this->order->order_number))
            ->line('Thank you for shopping with us!');
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'courier' => $this->order->shipping_courier,
            'tracking' => $this->order->tracking_number,
            'message' => 'Your order has been shipped'
        ];
    }
}