<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Video Service') }}</title>

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('js/video.js') }}" defer></script>

        <style>
            html {
                height: 100%;
            }
        </style>
    </head>
    <body {{ $attributes->merge(['class' => "h-full w-full font-sans text-gray-900 antialiased"]) }}>
        
        {{ $slot }}
        
    </body>
</html>
