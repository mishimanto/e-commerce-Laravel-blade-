@extends('layouts.admin')

@section('title', 'Campaigns')

@section('content')
<div class="">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Campaigns</h1>
        <a href="{{ route('admin.newsletter.campaigns.create') }}" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            New Campaign
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recipients</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sent/Failed</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scheduled</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($campaigns as $campaign)
                <tr>
                    <td class="px-6 py-4">{{ $campaign->subject }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($campaign->status === 'completed') bg-green-100 text-green-800
                            @elseif($campaign->status === 'sending') bg-blue-100 text-blue-800
                            @elseif($campaign->status === 'queued') bg-yellow-100 text-yellow-800
                            @elseif($campaign->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ $campaign->total_recipients }}</td>
                    <td class="px-6 py-4">
                        <span class="text-green-600">{{ $campaign->sent_count }}</span>
                        @if($campaign->failed_count > 0)
                            / <span class="text-red-600">{{ $campaign->failed_count }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($campaign->scheduled_at)
                            {{ $campaign->scheduled_at->format('M d, Y H:i') }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            {{-- View Icon --}}
                            <a href="{{ route('admin.newsletter.campaigns.show', $campaign) }}" 
                               class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-50 transition" 
                               title="View Campaign">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"/>
                                    <path d="M22 12c-2.667 4.667-6 7-10 7s-7.333-2.333-10-7c2.667-4.667 6-7 10-7s7.333 2.333 10 7z"/>
                                </svg>
                            </a>

                           

                            {{-- Delete Icon --}}
                            <form action="{{ route('admin.newsletter.campaigns.destroy', $campaign) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Are you sure you want to delete this campaign?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-50 transition"
                                        title="Delete Campaign">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"/>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                        <path d="M8 4V3c0-1 1-2 2-2h4c1 0 2 1 2 2v1"/>
                                        <line x1="10" x2="10" y1="11" y2="17"/>
                                        <line x1="14" x2="14" y1="11" y2="17"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-3">
                                <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"/>
                                <line x1="2" y1="8" x2="22" y2="8"/>
                                <line x1="2" y1="16" x2="22" y2="16"/>
                                <line x1="8" y1="2" x2="8" y2="22"/>
                                <line x1="16" y1="2" x2="16" y2="22"/>
                            </svg>
                            <p>No campaigns found.</p>
                            <a href="{{ route('admin.newsletter.campaigns.create') }}" class="text-blue-600 mt-2">
                                Create your first campaign
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($campaigns->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $campaigns->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Optional: Add this script for better delete confirmation --}}
@push('scripts')
<script>
    // Optional: You can add this if you want a custom delete confirmation
    document.querySelectorAll('.delete-campaign-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Are you sure you want to delete this campaign? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush
@endsection