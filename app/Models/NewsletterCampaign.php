<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Jobs\SendNewsletterCampaignJob; 
use App\Models\NewsletterSubscriber;


class NewsletterCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'content',
        'template',
        'filters',
        'total_recipients',
        'sent_count',
        'failed_count',
        'status',
        'scheduled_at',
        'sent_at'
    ];

    protected $casts = [
        'filters' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    /**
     * Get the subscribers for this campaign
     */
    public function getSubscribersQuery()
    {
        $query = NewsletterSubscriber::where('is_active', true);
        
        // Apply filters if any
        if ($this->filters) {
            if (isset($this->filters['verified'])) {
                if ($this->filters['verified'] === 'verified') {
                    $query->whereNotNull('verified_at');
                } elseif ($this->filters['verified'] === 'unverified') {
                    $query->whereNull('verified_at');
                }
            }
            
            if (isset($this->filters['date_from'])) {
                $query->where('created_at', '>=', $this->filters['date_from']);
            }
            
            if (isset($this->filters['date_to'])) {
                $query->where('created_at', '<=', $this->filters['date_to']);
            }
        }
        
        return $query;
    }

    /**
     * Get total subscribers count
     */
    public function getTotalSubscribersCount()
    {
        return $this->getSubscribersQuery()->count();
    }

    /**
     * Dispatch jobs for all subscribers
     */
    public function dispatchJobs()
    {
        $subscribers = $this->getSubscribersQuery()->get();
        $this->total_recipients = $subscribers->count();
        $this->status = 'queued';
        $this->save();
        
        foreach ($subscribers as $subscriber) {
            SendNewsletterCampaignJob::dispatch($this, $subscriber)
                ->onQueue('newsletter')
                ->delay(now()->addSeconds(rand(1, 3))); // Random delay to avoid overwhelming
        }
        
        $this->status = 'sending';
        $this->save();
        
        return $subscribers->count();
    }

    /**
     * Check if campaign is completed
     */
    public function checkCompletion()
    {
        if ($this->sent_count + $this->failed_count >= $this->total_recipients) {
            $this->status = 'completed';
            $this->sent_at = now();
            $this->save();
            return true;
        }
        return false;
    }
}