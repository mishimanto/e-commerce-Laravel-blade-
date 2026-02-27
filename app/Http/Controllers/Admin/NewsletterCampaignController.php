<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterCampaignController extends Controller
{
    /**
     * Display campaigns list
     */
    public function index()
    {
        $campaigns = NewsletterCampaign::orderBy('created_at', 'desc')->paginate(15);
        return view('admin.newsletter.campaigns.index', compact('campaigns'));
    }

    /**
     * Show create campaign form
     */
    public function create()
    {
        $totalSubscribers = NewsletterSubscriber::where('is_active', true)->count();
        $verifiedCount = NewsletterSubscriber::where('is_active', true)->whereNotNull('verified_at')->count();
        
        return view('admin.newsletter.campaigns.create', compact('totalSubscribers', 'verifiedCount'));
    }

    /**
     * Store campaign and dispatch jobs
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'send_now' => 'boolean',
            'scheduled_at' => 'nullable|date|after:now',
            'filters' => 'nullable|array'
        ]);

        $campaign = NewsletterCampaign::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'filters' => $request->filters,
            'status' => 'draft',
            'scheduled_at' => $request->scheduled_at
        ]);

        // Calculate total recipients
        $totalRecipients = $campaign->getTotalSubscribersCount();
        $campaign->total_recipients = $totalRecipients;
        $campaign->save();

        if ($request->send_now) {
            // Dispatch immediately
            $dispatched = $campaign->dispatchJobs();
            
            return redirect()->route('admin.newsletter.campaigns.show', $campaign)
                ->with('success', "Campaign created and queued for {$dispatched} subscribers!");
        }

        return redirect()->route('admin.newsletter.campaigns.show', $campaign)
            ->with('success', 'Campaign created as draft!');
    }

    /**
     * Show campaign details
     */
    public function show(NewsletterCampaign $campaign)
    {
        return view('admin.newsletter.campaigns.show', compact('campaign'));
    }

    /**
     * Send campaign now
     */
    public function sendNow(NewsletterCampaign $campaign)
    {
        if ($campaign->status !== 'draft' && $campaign->status !== 'cancelled') {
            return redirect()->back()->with('error', 'Campaign already sent or sending!');
        }

        $dispatched = $campaign->dispatchJobs();

        return redirect()->route('admin.newsletter.campaigns.show', $campaign)
            ->with('success', "Campaign queued for {$dispatched} subscribers!");
    }

    /**
     * Cancel campaign
     */
    public function cancel(NewsletterCampaign $campaign)
    {
        if ($campaign->status === 'sending' || $campaign->status === 'queued') {
            $campaign->status = 'cancelled';
            $campaign->save();
            
            return redirect()->back()->with('success', 'Campaign cancelled!');
        }

        return redirect()->back()->with('error', 'Campaign cannot be cancelled!');
    }

    /**
     * Delete campaign
     */
    public function destroy(NewsletterCampaign $campaign)
    {
        $campaign->delete();
        
        return redirect()->route('admin.newsletter.campaigns.index')
            ->with('success', 'Campaign deleted!');
    }

    /**
     * Get campaign progress (for AJAX)
     */
    public function progress(NewsletterCampaign $campaign)
    {
        // Force refresh from database
        $campaign->refresh();
        
        // Auto-complete if all jobs done
        if ($campaign->status === 'sending' || $campaign->status === 'queued') {
            $campaign->checkCompletion();
            $campaign->refresh();
        }
        
        return response()->json([
            'success' => true,
            'status' => $campaign->status,
            'sent_count' => (int) $campaign->sent_count,
            'failed_count' => (int) $campaign->failed_count,
            'total_recipients' => (int) $campaign->total_recipients,
            'progress' => $campaign->total_recipients > 0 
                ? round(($campaign->sent_count / $campaign->total_recipients) * 100) 
                : 0
        ]);
    }
}