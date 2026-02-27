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

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col justify-center items-center bg-gray-100">
            <div class="px-6 py-4 bg-white shadow-md rounded-lg">
                @yield('content') 
            </div>
        </div>
    </body>
</html>