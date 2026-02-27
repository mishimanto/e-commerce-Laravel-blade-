<?php
// app/Notifications/NewOrderNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification // ShouldQueue remove করলেন
{
    use Queueable;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'new_order',
            'icon' => 'order',
            'color' => 'blue',
            'title' => 'New Order',
            'message' => "New Order #{$this->order->order_number} Received",
            'amount' => $this->order->total,
            'customer_name' => $this->order->billing_name,
            'order_id' => $this->order->id, // এইটা যোগ করুন
            'link' => url("/admin/orders/{$this->order->id}"), // route() এর পরিবর্তে url() ব্যবহার করুন
            'time' => now()->toDateTimeString(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'new_order',
            'icon' => 'order',
            'color' => 'blue',
            'title' => 'New Order',
            'message' => "New Order #{$this->order->order_number} Received",
            'amount' =>  number_format($this->order->total, 2),
            'customer_name' => $this->order->billing_name,
            'order_id' => $this->order->id, // এইটা যোগ করুন
            'link' => url("/admin/orders/{$this->order->id}"), // route() এর পরিবর্তে url() ব্যবহার করুন
            'time' => now()->diffForHumans(),
        ]);
    }

    public function broadcastType()
    {
        return 'new-order';
    }
}