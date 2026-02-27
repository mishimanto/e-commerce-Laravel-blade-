@extends('layouts.app')

@section('title', 'Frequently Asked Questions - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-gray-400 to-gray-700 text-white overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')"></div>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-28 relative z-10">
        <div class="max-w-5xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-14 animate-fade-in">Frequently Asked Questions</h1>
            <!-- <p class="text-xl md:text-2xl text-blue-100 leading-relaxed">Your Trusted Partner for the Latest and Greatest in Technology Since 2020</p> -->
             <!-- Search Bar -->
            <div class="relative max-w-2xl mx-auto">
                <input type="text" 
                       id="faq-search" 
                       placeholder="Search your question..." 
                       class="w-full px-6 py-4 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-blue-300 shadow-lg">
                <svg class="absolute right-4 top-4 w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Categories -->
<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Category Navigation -->
        <div class="flex flex-wrap justify-center gap-3 mb-12">
            <button onclick="filterCategory('all')" 
                    class="category-filter active px-6 py-3 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition-all shadow-md hover:shadow-lg">
                All Questions
            </button>
            @foreach($faqCategories as $category)
                <button onclick="filterCategory('{{ Str::slug($category['category']) }}')" 
                        class="category-filter px-6 py-3 rounded-lg bg-white text-gray-700 font-medium hover:bg-gray-100 transition-all shadow-md hover:shadow-lg">
                    {{ $category['category'] }}
                </button>
            @endforeach
        </div>

        <!-- FAQ Accordion -->
        <div class="max-w-5xl mx-auto space-y-6">
            @foreach($faqCategories as $categoryIndex => $category)
                <div class="faq-category" data-category="{{ Str::slug($category['category']) }}">
                    <div class="bg-white shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-shadow">
                        <!-- Category Header -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center space-x-3">
                                @php
                                    $icons = [
                                        'shopping-cart' => '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>',
                                        'truck' => '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/><path d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>',
                                        'refresh-cw' => '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>',
                                        'headphones' => '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 14h3a2 2 0 012 2v3a2 2 0 01-2 2H5a2 2 0 01-2-2v-7a9 9 0 0118 0v7a2 2 0 01-2 2h-1a2 2 0 01-2-2v-3a2 2 0 012-2h3"/></svg>',
                                    ];
                                @endphp
                                {!! $icons[$category['icon']] ?? '' !!}
                                <h2 class="text-xl font-bold text-gray-800">{{ $category['category'] }}</h2>
                            </div>
                        </div>

                        <!-- FAQ Items -->
                        <div class="divide-y divide-gray-100">
                            @foreach($category['faqs'] as $index => $faq)
                                <div class="faq-item" x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            class="w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition-colors group">
                                        <span class="text-base font-medium text-gray-900 group-hover:text-blue-600 pr-8">{{ $faq['question'] }}</span>
                                        <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200 group-hover:text-blue-500" 
                                             :class="{ 'rotate-180': open }" 
                                             xmlns="http://www.w3.org/2000/svg" 
                                             fill="none" 
                                             viewBox="0 0 24 24" 
                                             stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" 
                                         x-cloak 
                                         x-transition:enter="transition ease-out duration-200"
                                         x-transition:enter-start="opacity-0 -translate-y-2"
                                         x-transition:enter-end="opacity-100 translate-y-0"
                                         class="px-8 pb-6 text-gray-400 leading-relaxed">
                                        {{ $faq['answer'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>        
    </div>    
</div>
 <!-- Still Have Questions -->
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 px-8 py-12 text-center text-white shadow-xl">
        <h2 class="text-2xl font-bold mb-5">Still have questions?</h2>
        <p class="text-blue-100 mb-8 max-w-2xl mx-auto">Can't find the answer you're looking for? Our support team is here to help 24/7.</p>
        <div class="flex flex-wrap gap-4 justify-center">
            <a href="{{ route('contact') }}" class="inline-flex items-center bg-white text-blue-600 px-6 py-3 font-semibold hover:bg-gray-100 transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                Live Chat
            </a>
            <a href="mailto:{{ config('settings.store_email', 'support@example.com') }}" class="inline-flex items-center bg-transparent border-2 border-white text-white px-6 py-3 font-semibold hover:bg-white hover:text-blue-600 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Email Us
            </a>
        </div>
    </div>

<!-- JavaScript for Filtering -->
<script>
    function filterCategory(category) {
        // Update active filter button
        document.querySelectorAll('.category-filter').forEach(btn => {
            btn.classList.remove('active', 'bg-blue-600', 'text-white');
            btn.classList.add('bg-white', 'text-gray-700');
        });
        event.target.classList.add('active', 'bg-blue-600', 'text-white');
        event.target.classList.remove('bg-white', 'text-gray-700');
        
        // Show/hide categories
        document.querySelectorAll('.faq-category').forEach(cat => {
            if (category === 'all' || cat.dataset.category === category) {
                cat.style.display = 'block';
            } else {
                cat.style.display = 'none';
            }
        });
    }
    
    // Search functionality
    document.getElementById('faq-search').addEventListener('keyup', function() {
        let searchTerm = this.value.toLowerCase();
        
        document.querySelectorAll('.faq-item').forEach(item => {
            let question = item.querySelector('button span').textContent.toLowerCase();
            let answer = item.querySelector('.px-6.pb-6').textContent.toLowerCase();
            
            if (question.includes(searchTerm) || answer.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show/hide categories based on visible items
        document.querySelectorAll('.faq-category').forEach(cat => {
            let hasVisible = false;
            cat.querySelectorAll('.faq-item').forEach(item => {
                if (item.style.display !== 'none') hasVisible = true;
            });
            cat.style.display = hasVisible ? 'block' : 'none';
        });
    });
</script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection