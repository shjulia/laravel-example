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
    @if($allowPwa)
        @laravelPWA
    @endif
</head>
<body>
<div id="app">
    <main class="dark">
        <div class="container">
            <div class="row justify-content-center align-items-center cardrow">
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="detailsh2">{{ $h2 }}</h2>
                                @include('layouts._betaAlert')
                                @include('layouts._errors')
                                @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="{{ mix('js/app.js', 'build') }}" defer></script>
@stack('custom-scripts')
</body>
</html>
