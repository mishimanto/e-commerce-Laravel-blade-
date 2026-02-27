<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- jQuery FIRST -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Summernote -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote.min.js"></script>

    @if(!empty(config('settings.store_favicon')))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . config('settings.store_favicon')) }}">
        <link rel="shortcut icon" href="{{ asset('storage/' . config('settings.store_favicon')) }}">
        <link rel="apple-touch-icon" href="{{ asset('storage/' . config('settings.store_favicon')) }}">
    @else
        {{-- Default favicon --}}
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @endif
    
    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>
    
    {{-- Styles --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    
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

    {{-- Main Layout --}}
    <div class="min-h-screen flex" x-data="{ sidebarOpen: true }">
        {{-- Sidebar --}}
        <div x-show="sidebarOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-50">
            <x-admin.sidebar :pending-orders="$pendingOrders ?? 0" />
        </div>

        {{-- Main Content --}}
        <div class="flex-1" :class="{ 'ml-64': sidebarOpen }">
            {{-- Top Navigation --}}
            <x-admin.navbar />

            {{-- Page Content --}}
            <main class="p-6 min-h-screen">
                {{-- Breadcrumb --}}
                @if(isset($breadcrumbs))
                    <x-breadcrumb :items="$breadcrumbs" />
                @endif

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

                {{-- Main Content --}}
                @yield('content')
            </main>

            {{-- Footer --}}
            <x-admin.footer />
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @vite(['resources/js/app.js'])
    @stack('scripts')

    {{-- Alpine.js Store for Sidebar Toggle --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sidebar', () => ({
                open: true,
                toggle() {
                    this.open = !this.open;
                }
            }));

            Alpine.store('sidebar', {
                open: true,
                toggle() {
                    this.open = !this.open;
                }
            });

            window.addEventListener('toggle-sidebar', () => {
                Alpine.store('sidebar').toggle();
            });
        });
    </script>
</body>
</html>