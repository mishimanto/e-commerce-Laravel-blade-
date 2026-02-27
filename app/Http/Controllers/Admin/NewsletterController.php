<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NewsletterController extends Controller
{
    /**
     * Display a listing of newsletter subscribers
     */
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query();

        // Search by email
        if ($request->has('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        $query->orderBy($sort, $direction);

        $subscribers = $query->paginate(15);

        return view('admin.newsletter.index', compact('subscribers'));
    }

    /**
     * Remove the specified subscriber
     */
    public function destroy($id)
    {
        try {
            $subscriber = NewsletterSubscriber::findOrFail($id);
            $subscriber->delete();

            // Check if AJAX request
            if (request()->wantsJson() || request()->ajax()) {
                // Store success message in session
                session()->flash('success', 'Subscriber deleted successfully.');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Subscriber deleted successfully.'
                ]);
            }

            return redirect()->route('admin.newsletter.index')
                ->with('success', 'Subscriber deleted successfully.');

        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                // Store error message in session
                session()->flash('error', 'Failed to delete subscriber.');
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete subscriber.'
                ], 500);
            }

            return redirect()->route('admin.newsletter.index')
                ->with('error', 'Failed to delete subscriber.');
        }
    }

    /**
     * Export subscribers to CSV
     */
    public function export()
    {
        $subscribers = NewsletterSubscriber::where('is_active', true)->get();
        
        $filename = 'newsletter-subscribers-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($subscribers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Email', 'Name', 'Status', 'Verified At', 'Subscribed Date']);
            
            foreach ($subscribers as $subscriber) {
                fputcsv($handle, [
                    $subscriber->email,
                    $subscriber->name ?? 'N/A',
                    $subscriber->is_active ? 'Active' : 'Inactive',
                    $subscriber->verified_at ? $subscriber->verified_at->format('Y-m-d H:i:s') : 'Not Verified',
                    $subscriber->created_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Toggle subscriber status
     */
    public function toggleStatus($id)
    {
        try {
            $subscriber = NewsletterSubscriber::findOrFail($id);
            $subscriber->is_active = !$subscriber->is_active;
            $subscriber->save();

            // Store success message in session
            session()->flash('success', 'Status updated successfully.');

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'status' => $subscriber->is_active
            ]);
        } catch (\Exception $e) {
            // Store error message in session
            session()->flash('error', 'Failed to update status.');

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.'
            ], 500);
        }
    }

    /**
     * Bulk delete subscribers
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:newsletter_subscribers,id'
        ]);

        try {
            NewsletterSubscriber::whereIn('id', $request->ids)->delete();

            // Store success message in session
            session()->flash('success', 'Subscribers deleted successfully.');

            return response()->json([
                'success' => true,
                'message' => 'Subscribers deleted successfully.'
            ]);
        } catch (\Exception $e) {
            // Store error message in session
            session()->flash('error', 'Failed to delete subscribers.');

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subscribers.'
            ], 500);
        }
    }

    public function unsubscribe($email)
    {
        $subscriber = NewsletterSubscriber::where('email', $email)->first();
        
        if ($subscriber) {
            $subscriber->update([
                'is_active' => false
            ]);
            
            return redirect()->route('home')->with('success', 'You have been unsubscribed successfully.');
        }
        
        return redirect()->route('home')->with('error', 'Email not found in our subscriber list.');
    }
}