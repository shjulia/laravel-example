<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Boon') }}</title>
    <link href="{{ mix('css/app.css', 'build') }}" rel="stylesheet">
    <meta name="apple-itunes-app" content="app-id=1471640517, app-argument={{ config('app.url') }}">
    {!! $analytics !!}
    @laravelPWA
</head>
<body>
<div id="app">
    <main>
        @include('layouts._errors')
        @include('layouts._betaAlert')
        @yield('content')
    </main>
</div>
@if (env("MIX_ALLOW_FB_TRACK"))
    <script>
        window.fbAsyncInit = function() {
            FB.init({
                appId: '715421882272409',
                xfbml: true,
                version: 'v5.0'
            });
            FB.AppEvents.logPageView();
        };
    </script>
    <script async defer src="https://connect.facebook.net/en_US/sdk.js"></script>
@endif
<script src="{{ mix('js/registration.js', 'build') }}" defer></script>
@stack('custom-scripts')
</body>
</html>
