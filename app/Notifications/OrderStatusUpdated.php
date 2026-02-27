<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $oldStatus;

    public function __construct(Order $order, $oldStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Status Updated - ' . $this->order->order_number)
            ->greeting('Dear ' . $this->order->billing_name . '!')
            ->line('The status of your order has been updated.')
            ->line('Order Number: ' . $this->order->order_number)
            ->line('Previous Status: ' . ucfirst($this->oldStatus))
            ->line('Current Status: ' . ucfirst($this->order->status))
            ->action('Track Order', route('order.track', $this->order->order_number));
    }

    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->order->status,
            'message' => 'Order status updated from ' . $this->oldStatus . ' to ' . $this->order->status
        ];
    }
}