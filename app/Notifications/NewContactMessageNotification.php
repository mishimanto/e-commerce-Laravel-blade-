<?php
// app/Notifications/NewContactMessageNotification.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification // ShouldQueue remove করলেন
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'contact_message',
            'icon' => 'message',
            'color' => 'green',
            'title' => 'New Message',
            'message' => "New Message from {$this->message->name}",
            'subject' => $this->message->subject,
            'email' => $this->message->email,
            'message_id' => $this->message->id, // এইটা যোগ করুন
            'link' => url("/admin/contact-messages/{$this->message->id}"), // route() এর পরিবর্তে url() ব্যবহার করুন
            'time' => now()->toDateTimeString(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'contact_message',
            'icon' => 'message',
            'color' => 'green',
            'title' => 'New Message',
            'message' => "New Message from {$this->message->name}",
            'subject' => $this->message->subject,
            'email' => $this->message->email,
            'message_id' => $this->message->id, // এইটা যোগ করুন
            'link' => url("/admin/contact-messages/{$this->message->id}"), // route() এর পরিবর্তে url() ব্যবহার করুন
            'time' => now()->diffForHumans(),
        ]);
    }

    public function broadcastType()
    {
        return 'new-contact-message';
    }
}