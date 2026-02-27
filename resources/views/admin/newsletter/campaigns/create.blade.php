@extends('layouts.admin')

@section('title', 'Create Newsletter Campaign')

@section('content')
<div class="">
    <div class="mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Create New Campaign</h1>
            <a href="{{ route('admin.newsletter.campaigns.index') }}" 
               class="text-gray-600 hover:text-gray-900 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Campaigns
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            {{-- Recipients Summary --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-800 mb-2">Recipients Summary</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Total Active Subscribers</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $totalSubscribers }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Verified Subscribers</p>
                        <p class="text-2xl font-bold text-green-600">{{ $verifiedCount }}</p>
                    </div>
                </div>
            </div>
            
            {{-- Campaign Form --}}
            <form action="{{ route('admin.newsletter.campaigns.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                    <input type="text" 
                           name="subject" 
                           value="{{ old('subject') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('subject') border-red-500 @enderror"
                           placeholder="Enter email subject"
                           required>
                    @error('subject')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Content</label>
                    <textarea name="content" 
                              rows="10" 
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 @error('content') border-red-500 @enderror"
                              placeholder="Write your email content here..."
                              required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 mt-1">You can use HTML tags for formatting</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Target Audience</label>
                    <div class="space-y-2">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="filters[audience]" value="all" checked class="mr-3">
                            <div>
                                <span class="font-medium">All Active Subscribers</span>
                                <p class="text-sm text-gray-500">{{ $totalSubscribers }} recipients</p>
                            </div>
                        </label>
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="filters[audience]" value="verified" class="mr-3">
                            <div>
                                <span class="font-medium">Verified Only</span>
                                <p class="text-sm text-gray-500">{{ $verifiedCount }} recipients</p>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Schedule (Optional)</label>
                    <input type="datetime-local" 
                           name="scheduled_at" 
                           value="{{ old('scheduled_at') }}"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                           min="{{ now()->format('Y-m-d\TH:i') }}">
                    <p class="text-xs text-gray-500 mt-1">Leave empty to send immediately</p>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="send_now" value="1" checked class="mr-2">
                            <span class="text-sm font-medium">Send immediately</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">If unchecked, campaign will be saved as draft</p>
                    </div>
                    
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                        </svg>
                        Create Campaign
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection