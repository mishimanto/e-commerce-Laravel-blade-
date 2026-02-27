@extends('layouts.admin')

@section('title', 'Campaign Details - ' . $campaign->subject)

@section('content')
<div class="">
    <div class="mx-auto">
        {{-- Header with Back Button --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Campaign Details</h1>
            <a href="{{ route('admin.newsletter.campaigns.index') }}" 
               class="text-gray-600 hover:text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Campaigns
            </a>
        </div>
        
        {{-- Campaign Title --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800">{{ $campaign->subject }}</h2>
            <p class="text-sm text-gray-500 mt-1">Created: {{ $campaign->created_at->format('M d, Y H:i') }}</p>
        </div>
        
        {{-- Progress Card with Alpine.js --}}
        <div x-data="campaignProgress({{ $campaign->id }}, '{{ $campaign->status }}', {{ $campaign->sent_count }}, {{ $campaign->total_recipients }}, {{ $campaign->failed_count }})" 
             x-init="init()"
             class="bg-white rounded-lg shadow-sm p-6 mb-6">
            
            <h3 class="font-semibold text-lg mb-4">Campaign Progress</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <p class="text-sm text-blue-600 mb-1">Status</p>
                    <p class="text-xl font-bold capitalize">
                        <span class="px-3 py-1 text-sm rounded-full" 
                              :class="{
                                  'bg-green-100 text-green-800': status === 'completed',
                                  'bg-blue-100 text-blue-800': status === 'sending',
                                  'bg-yellow-100 text-yellow-800': status === 'queued',
                                  'bg-red-100 text-red-800': status === 'cancelled',
                                  'bg-gray-100 text-gray-800': status === 'draft'
                              }"
                              x-text="status.charAt(0).toUpperCase() + status.slice(1)">
                        </span>
                    </p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <p class="text-sm text-green-600 mb-1">Total Recipients</p>
                    <p class="text-xl font-bold" x-text="totalRecipients">{{ $campaign->total_recipients }}</p>
                </div>
                <div class="bg-indigo-50 p-4 rounded-lg">
                    <p class="text-sm text-indigo-600 mb-1">Sent</p>
                    <p class="text-xl font-bold" x-text="sentCount">{{ $campaign->sent_count }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <p class="text-sm text-red-600 mb-1">Failed</p>
                    <p class="text-xl font-bold" x-text="failedCount">{{ $campaign->failed_count }}</p>
                </div>
            </div>
            
            {{-- Progress Bar --}}
            <div class="mb-2">
                <div class="flex justify-between text-sm mb-1">
                    <span>Progress</span>
                    <span><span x-text="progress"></span>% (<span x-text="sentCount"></span>/<span x-text="totalRecipients"></span>)</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" 
                         :style="'width: ' + progress + '%'"></div>
                </div>
            </div>

            {{-- Action Buttons for Draft Campaigns --}}
            <template x-if="status === 'draft'">
                <div class="mt-6 flex gap-3">
                    <form action="{{ route('admin.newsletter.campaigns.send', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition flex items-center gap-2"
                                onclick="return confirm('Are you sure you want to send this campaign to {{ $campaign->total_recipients }} subscribers?')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                            </svg>
                            Send Now
                        </button>
                    </form>
                    
                    <a href="#" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                        </svg>
                        Edit
                    </a>
                </div>
            </template>

            {{-- Cancel Button for Sending Campaigns --}}
            <template x-if="status === 'sending' || status === 'queued'">
                <div class="mt-6">
                    <form action="{{ route('admin.newsletter.campaigns.cancel', $campaign) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition flex items-center gap-2"
                                onclick="return confirm('Are you sure you want to cancel this campaign?')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            Cancel Campaign
                        </button>
                    </form>
                </div>
            </template>
        </div>
        
        {{-- Campaign Details --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="font-semibold text-lg mb-4">Campaign Details</h3>
            
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Subject</p>
                    <p class="font-medium">{{ $campaign->subject }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Target Audience</p>
                    <p class="font-medium">
                        @if(isset($campaign->filters['audience']) && $campaign->filters['audience'] === 'verified')
                            Verified Subscribers Only
                        @else
                            All Active Subscribers
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Scheduled Time</p>
                    <p class="font-medium">
                        @if($campaign->scheduled_at)
                            {{ $campaign->scheduled_at->format('M d, Y H:i') }}
                        @else
                            <span class="text-gray-400">Immediate</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Sent At</p>
                    <p class="font-medium">
                        @if($campaign->sent_at)
                            {{ $campaign->sent_at->format('M d, Y H:i') }}
                        @else
                            <span class="text-gray-400" x-text="status === 'completed' ? 'Completed' : 'Not sent yet'"></span>
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-2">Email Content</p>
                <div class="border rounded-lg p-6 bg-gray-50 prose max-w-none">
                    {!! $campaign->content !!}
                </div>
            </div>
        </div>
        
        {{-- Delete Option --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h4 class="font-medium text-gray-900">Delete Campaign</h4>
                    <p class="text-sm text-gray-500">Once deleted, this campaign cannot be recovered.</p>
                </div>
                <form action="{{ route('admin.newsletter.campaigns.destroy', $campaign) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-100 text-red-600 px-4 py-2 rounded-lg hover:bg-red-200 transition"
                            onclick="return confirm('Are you sure you want to delete this campaign?')">
                        Delete Campaign
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function campaignProgress(campaignId, initialStatus, initialSent, initialTotal, initialFailed) {
    return {
        campaignId: campaignId,
        status: initialStatus,
        sentCount: initialSent,
        totalRecipients: initialTotal,
        failedCount: initialFailed,
        pollingInterval: null,
        
        init() {
            if (this.status === 'sending' || this.status === 'queued') {
                this.startPolling();
            }
        },
        
        get progress() {
            if (this.totalRecipients === 0) return 0;
            return Math.round((this.sentCount / this.totalRecipients) * 100);
        },
        
        startPolling() {
            this.pollingInterval = setInterval(() => {
                this.fetchProgress();
            }, 3000); // Poll every 3 seconds
        },
        
        fetchProgress() {
            fetch(`/admin/newsletter/campaigns/${this.campaignId}/progress`)
                .then(response => response.json())
                .then(data => {
                    this.status = data.status;
                    this.sentCount = data.sent_count;
                    this.failedCount = data.failed_count;
                    
                    // Stop polling if completed or cancelled
                    if (this.status === 'completed' || this.status === 'cancelled') {
                        clearInterval(this.pollingInterval);
                    }
                })
                .catch(error => console.error('Error fetching progress:', error));
        }
    }
}
</script>
@endsection