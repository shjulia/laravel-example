<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Boon</title>
    <link href="{{ asset('build/css/app.css') }}" rel="stylesheet">

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light main-navbar">
            <div class="container-fluid">
                <a class="navbar-brand mx-auto" href="{{ route('home') }}">
                    <img src="{{ asset('/img/boon-logo.svg') }}">
                </a>
                <button class="navbar-toggler"
                        type="button"
                        data-toggle="collapse"
                        data-target="#main-navbar_content"
                        aria-controls="main-navbar_content"
                        aria-expanded="false"
                        aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

            </div>
        </nav>
        <main>
            @yield('content')
        </main>
    </div>
</body>
</html>