<?php
// app/Http/Controllers/Admin/NotificationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Display the specified notification.
     */
    public function show($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        
        // Mark as read when viewed
        if (!$notification->read_at) {
            $notification->markAsRead();
        }
        
        // Redirect to the link if exists
        if (isset($notification->data['link'])) {
            return redirect($notification->data['link']);
        }
        
        // Otherwise redirect back with notification data (you can create a show page if needed)
        return redirect()->back()->with('info', 'Notification viewed');
    }

    /**
     * Get unread notifications count.
     */
    public function getUnreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications->count()
        ]);
    }

    /**
     * Get latest notifications for dropdown.
     */
    public function getNotifications()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }
        
        return redirect()->back()->with('success', 'Notification deleted successfully.');
    }

    /**
     * Bulk delete notifications.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'string'
        ]);

        $deleted = Auth::user()->notifications()
            ->whereIn('id', $request->ids)
            ->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$deleted} notifications deleted successfully.",
                'count' => $deleted
            ]);
        }

        return redirect()->back()->with('success', "{$deleted} notifications deleted successfully.");
    }

    /**
     * Clear all notifications.
     */
    public function clearAll()
    {
        $count = Auth::user()->notifications()->count();
        Auth::user()->notifications()->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$count} notifications cleared successfully.",
                'count' => $count
            ]);
        }
        
        return redirect()->back()->with('success', "{$count} notifications cleared successfully.");
    }
}