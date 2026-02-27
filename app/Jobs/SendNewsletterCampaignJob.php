<?php

namespace App\Jobs;

use App\Models\NewsletterCampaign;
use App\Models\NewsletterSubscriber;
use App\Mail\NewsletterMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendNewsletterCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;
    protected $subscriber;
    public $timeout = 120;
    public $tries = 3;

    public function __construct(NewsletterCampaign $campaign, NewsletterSubscriber $subscriber)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;
    }

    public function handle(): void
    {
        try {
            // Send email to single subscriber
            Mail::to($this->subscriber->email)->send(new NewsletterMail($this->campaign, $this->subscriber));
            
            // Update campaign stats
            $this->campaign->increment('sent_count');
            
            Log::info('Newsletter sent successfully', [
                'campaign_id' => $this->campaign->id,
                'subscriber_id' => $this->subscriber->id,
                'email' => $this->subscriber->email
            ]);
            
        } catch (\Exception $e) {
            Log::error('Newsletter send failed: ' . $e->getMessage(), [
                'campaign_id' => $this->campaign->id,
                'subscriber_id' => $this->subscriber->id,
                'email' => $this->subscriber->email
            ]);
            
            $this->campaign->increment('failed_count');
            
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Newsletter job failed permanently: ' . $exception->getMessage(), [
            'campaign_id' => $this->campaign->id,
            'subscriber_id' => $this->subscriber->id
        ]);
        
        $this->campaign->refresh();
        $this->campaign->increment('failed_count');
        $this->campaign->checkCompletion();
    }
}