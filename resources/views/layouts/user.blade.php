<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(!empty(config('settings.store_favicon')))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . config('settings.store_favicon')) }}">
        <link rel="shortcut icon" href="{{ asset('storage/' . config('settings.store_favicon')) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . config('settings.store_favicon')) }}">
    @else
        {{-- Default favicon --}}
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @endif

    <title>@yield('title', 'User Dashboard') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        {{-- User Header Component --}}
        <x-user-header :user="auth()->user()" />
        
        {{-- Page Loader --}}
        <div 
            x-data="{ loading: true }"
            x-init="
                window.addEventListener('load', () => {
                    setTimeout(() => loading = false, 800);
                });
                
                if (document.readyState === 'complete') {
                    setTimeout(() => loading = false, 800);
                }
            "
            x-show="loading"
            x-transition:leave="transition ease-in-out duration-700"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-110"
            class="fixed inset-0 z-[9999]"
        >
            <x-loader.spinner 
                size="xl"
                fullScreen
                image="{{ asset('storage/images/logo.webp') }}"  
            />
        </div>

        {{-- Page Content --}}
            <main class="p-6 min-h-screen">
                <div class="container mx-auto">
                {{-- Alerts --}}
                @if(session('success'))
                    <x-alert type="success" :message="session('success')" dismissible />
                @endif

                @if(session('error'))
                    <x-alert type="error" :message="session('error')" dismissible />
                @endif

                @if(session('warning'))
                    <x-alert type="warning" :message="session('warning')" dismissible />
                @endif

                @if(session('info'))
                    <x-alert type="info" :message="session('info')" dismissible />
                @endif
                </div>
                

                {{-- Main Content --}}
                @yield('content')
            </main>
    </div>

    @stack('scripts')
</body>
</html>