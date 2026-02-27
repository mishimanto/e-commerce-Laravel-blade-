
@extends('layouts.admin')

@section('title', 'Message Details')

@section('content')
<div class="">
    <!-- Breadcrumb -->
    <!-- <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900">Message Details</h1>
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('admin.contact-messages.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ml-2">Contact Messages</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Message Details</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div> -->

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content - Message Details & Reply -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Message Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                    <h5 class="text-lg font-medium text-gray-900">Message from {{ $contactMessage->name }}</h5>
                    <button type="button" 
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white 
                                   {{ $contactMessage->is_read ? 'bg-gray-600 hover:bg-gray-700' : 'bg-yellow-600 hover:bg-yellow-700' }} 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 toggle-read"
                            data-id="{{ $contactMessage->id }}">
                        @if($contactMessage->is_read)
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                            </svg>
                        @else
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                        @endif
                        Mark as {{ $contactMessage->is_read ? 'Unread' : 'Read' }}
                    </button>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="text-sm font-medium text-gray-900">{{ $contactMessage->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="text-sm font-medium text-gray-900">
                                <a href="mailto:{{ $contactMessage->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $contactMessage->email }}
                                </a>
                            </p>
                        </div>
                        @if($contactMessage->phone)
                        <div>
                            <p class="text-sm text-gray-500">Phone</p>
                            <p class="text-sm font-medium text-gray-900">
                                <a href="tel:{{ $contactMessage->phone }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $contactMessage->phone }}
                                </a>
                            </p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Subject</p>
                            <p class="text-sm font-medium text-gray-900">{{ $contactMessage->subject }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Received</p>
                            <p class="text-sm font-medium text-gray-900">{{ $contactMessage->created_at->format('F j, Y, g:i a') }}</p>
                        </div>
                        @if($contactMessage->is_replied)
                        <div>
                            <p class="text-sm text-gray-500">Replied</p>
                            <p class="text-sm font-medium text-gray-900">{{ $contactMessage->replied_at->format('F j, Y, g:i a') }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-2">Message:</p>
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $contactMessage->message }}</p>
                    </div>
                </div>
            </div>

            <!-- Reply Section -->
            @if(!$contactMessage->is_replied)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h5 class="text-lg font-medium text-gray-900">Send Reply</h5>
                </div>
                <div class="px-6 py-4">
                    <form action="{{ route('admin.contact-messages.reply', $contactMessage) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="reply_message" class="block text-sm font-medium text-gray-700 mb-1">Reply Message</label>
                            <textarea name="reply_message" 
                                      id="reply_message" 
                                      rows="5" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('reply_message') border-red-500 @enderror" 
                                      required>{{ old('reply_message') }}</textarea>
                            @error('reply_message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" 
                                       name="send_email" 
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                       checked>
                                <span class="ml-2 text-sm text-gray-600">Send email notification to customer</span>
                            </label>
                        </div>

                        <div class="flex items-center space-x-3">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Send Reply
                            </button>
                            <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h5 class="text-lg font-medium text-gray-900">Reply Sent</h5>
                </div>
                <div class="px-6 py-4">
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-sm font-medium text-gray-700 mb-2">Your Reply:</p>
                        <p class="text-sm text-gray-600 whitespace-pre-line mb-3">{{ $contactMessage->reply_message }}</p>
                        <p class="text-xs text-gray-500">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Sent on {{ $contactMessage->replied_at->format('F j, Y, g:i a') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar - Customer Info & Actions -->
        <div class="space-y-6">
            <!-- Customer Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h5 class="text-lg font-medium text-gray-900">Customer Information</h5>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500">Name</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $contactMessage->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $contactMessage->email }}</dd>
                        </div>
                        @if($contactMessage->phone)
                        <div>
                            <dt class="text-sm text-gray-500">Phone</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $contactMessage->phone }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h5 class="text-lg font-medium text-gray-900">Quick Actions</h5>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <a href="mailto:{{ $contactMessage->email }}" target="_blank" 
                       class="inline-flex items-center w-full justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Open in Mail Client
                    </a>
                    
                    @if($contactMessage->phone)
                        <a href="tel:{{ $contactMessage->phone }}" 
                           class="inline-flex items-center w-full justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            Call Customer
                        </a>
                    @endif
                    
                    <button type="button" 
                            class="inline-flex items-center w-full justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150 delete-message"
                            data-id="{{ $contactMessage->id }}">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Message
                    </button>
                </div>
            </div>

            <!-- Message Info Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h5 class="text-lg font-medium text-gray-900">Message Info</h5>
                </div>
                <div class="px-6 py-4">
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">Status</dt>
                            <dd>
                                @if(!$contactMessage->is_read)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                        </svg>
                                        Unread
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path>
                                        </svg>
                                        Read
                                    </span>
                                @endif
                                
                                @if($contactMessage->is_replied)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-1">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                        </svg>
                                        Replied
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-1">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        Not Replied
                                    </span>
                                @endif
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm text-gray-500">Received</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $contactMessage->created_at->format('M j, Y') }}</dd>
                            <dd class="text-xs text-gray-500">{{ $contactMessage->created_at->diffForHumans() }}</dd>
                        </div>
                        
                        @if($contactMessage->is_replied)
                        <div>
                            <dt class="text-sm text-gray-500">Replied</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $contactMessage->replied_at->format('M j, Y') }}</dd>
                            <dd class="text-xs text-gray-500">{{ $contactMessage->replied_at->diffForHumans() }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Read/Unread
    const toggleReadBtn = document.querySelector('.toggle-read');
    if (toggleReadBtn) {
        toggleReadBtn.addEventListener('click', function() {
            const messageId = this.dataset.id;
            
            fetch(`{{ url('admin/contact-messages') }}/${messageId}/toggle-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    }

    // Delete Message
    const deleteBtn = document.querySelector('.delete-message');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            const messageId = this.dataset.id;
            
            if (confirm('Are you sure you want to delete this message?')) {
                fetch(`{{ url('admin/contact-messages') }}/${messageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '{{ route("admin.contact-messages.index") }}';
                    } else {
                        throw new Error('Delete failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting. Please try again.');
                });
            }
        });
    }
});
</script>
@endpush