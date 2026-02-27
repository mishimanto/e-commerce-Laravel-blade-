<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $reason;

    public function __construct(Order $order, $reason = null)
    {
        $this->order = $order;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Order Cancelled - ' . $this->order->order_number)
            ->greeting('Dear ' . $this->order->billing_name . '!')
            ->line('Your order has been cancelled.')
            ->line('Order Number: ' . $this->order->order_number);

        if ($this->reason) {
            $message->line('Reason: ' . $this->reason);
        }

        $message->line('If you have any questions, please contact our customer support.')
            ->action('Contact Support', route('contact'));

        return $message;
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'reason' => $this->reason,
            'message' => 'Order has been cancelled'
        ];
    }
}