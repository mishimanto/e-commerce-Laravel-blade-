<?php
// app/Http/Controllers/Admin/ContactMessageController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReply;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages.
     */
    public function index(Request $request)
    {
        $query = ContactMessage::latest();

        // Filter by read status
        if ($request->has('filter')) {
            if ($request->filter == 'unread') {
                $query->where('is_read', false);
            } elseif ($request->filter == 'read') {
                $query->where('is_read', true);
            } elseif ($request->filter == 'replied') {
                $query->where('is_replied', true);
            } elseif ($request->filter == 'unreplied') {
                $query->where('is_replied', false);
            }
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(15);

        // Statistics
        $totalMessages = ContactMessage::count();
        $unreadMessages = ContactMessage::where('is_read', false)->count();
        $repliedMessages = ContactMessage::where('is_replied', true)->count();

        return view('admin.contact-messages.index', compact('messages', 'totalMessages', 'unreadMessages', 'repliedMessages'));
    }

    /**
     * Display the specified message.
     */
    public function show(ContactMessage $contactMessage)
    {
        // Mark as read when viewed
        if (!$contactMessage->is_read) {
            $contactMessage->update(['is_read' => true]);
        }

        return view('admin.contact-messages.show', compact('contactMessage'));
    }

    public function reply(Request $request, ContactMessage $contactMessage)
    {
        $request->validate([
            'reply_message' => 'required|string',
        ]);

        // Database-এ reply save করুন
        $contactMessage->update([
            'reply_message' => $request->reply_message,
            'is_replied' => true,
            'replied_at' => now(),
        ]);

        // Email পাঠান (এই অংশটা নতুন)
        try {
            Mail::to($contactMessage->email)->send(new ContactReply(
                $contactMessage->name,
                $request->reply_message,
                $contactMessage->subject
            ));
            
            $message = 'Reply sent successfully and email delivered.';
        } catch (\Exception $e) {
            $message = 'Reply saved but email delivery failed. Please check mail configuration.';
        }

        return redirect()->route('admin.contact-messages.show', $contactMessage)
            ->with('success', $message);
    }

    /**
     * Remove the specified message.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')
            ->with('success', 'Message deleted successfully.');
    }

    /**
     * Bulk delete messages.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:contact_messages,id'
        ]);

        ContactMessage::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Selected messages deleted successfully.'
        ]);
    }

    /**
     * Mark as read/unread.
     */
    public function toggleRead(ContactMessage $contactMessage)
    {
        $contactMessage->update([
            'is_read' => !$contactMessage->is_read
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message status updated.',
            'is_read' => $contactMessage->is_read
        ]);
    }

    public function getUnreadCount()
    {
        $count = ContactMessage::where('is_read', false)->count();
        
        return response()->json(['count' => $count]);
    }
}