<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Boon') }}</title>
    <link href="{{ mix('css/app.css', 'build') }}" rel="stylesheet">
    <meta name="apple-itunes-app" content="app-id=1471640517, app-argument={{ config('app.url') }}">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    {!! $analytics !!}
    @if($allowPwa)
        @laravelPWA
    @endif
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light main-navbar">
            <div class="container-fluid">
                <ul class="navbar-nav mr-auto">
                    <a class="home" href="{{ route('home') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
                </ul>
                <a class="navbar-brand mx-auto" href="{{ route('home') }}"><img src="{{ asset('/img/boon-logo.svg') }}"></a>

                <div class="navbar-collapse" id="main-navbar_content">

                    <ul class="navbar-nav ml-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('signup.userBase') }}">Sign up</a>
                            </li>
                        @else
                            <user-component :user="{{ Auth::user() }}" action="{{ route('setTimeDiff') }}" server-date="{{ date('Y-m-d') }}"></user-component>
                            @can('admin-analytics')
                                <li class="nav-item dropdown">
                                    <a id="analDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        Analytics <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="analDropdown">
                                        <a class="dropdown-item" href="{{ route('admin.analytics.index') }}">
                                            Analytics
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.analytics.emails.index') }}">
                                            Marketing emails
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.analytics.map.signups-areas') }}">
                                            Sign-ups map by areas
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.analytics.map.signups') }}">
                                            Sign-ups map
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.analytics.available') }}">
                                            Available
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.analytics.logs.index') }}">
                                            Logs
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.analytics.queue-logs') }}">
                                            Queue logs
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.analytics.login-log') }}">
                                            Login logs
                                        </a>
                                    </div>
                                </li>
                            @endcan
                            @can('view-transactions')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.analytics.transactions.practices') }}">Transactions</a>
                                </li>
                            @endcan
                            @can('view-shifts')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.shifts.index') }}">Shifts</a>
                                </li>
                            @endcan
                            @can('manage-shifts')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.coupons.index') }}">Coupons</a>
                                </li>
                            @endcan
                            @can('view-users')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.users.index') }}">Users</a>
                                </li>
                            @endcan
                            @can('manage-users')
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.users.autosaves') }}">Auto saves</a>
                                </li>
                            @endcan
                            @can('manage-machine-learning')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.ml') }}">ML</a>
                            </li>
                            @endcan
                            @can('manage-data')
                                <li class="nav-item dropdown">
                                    <a id="dataDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        Data <span class="caret"></span>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dataDropdown">
                                        <a class="dropdown-item" href="{{ route('admin.newsletter.template.index') }}">
                                            Email Templates
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.terms.index') }}">
                                            Terms and conditions
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.privacy.index') }}">
                                            Privacy policy
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.industries.index') }}">
                                            Industries
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.positions.index') }}">
                                            Positions
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.tools.index') }}">
                                            Management Software
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.rates.index') }}">
                                            Rates
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.specialities.index') }}">
                                            Specialities
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.license_types.index') }}">
                                            License Types
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.tasks.index') }}">
                                            Routine tasks
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.location.region.index') }}">
                                            Regions
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.location.state.index') }}">
                                            States
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.data.scores.index') }}">
                                            Score bubbles
                                        </a>
                                        <a class="dropdown-item" href="{{ route('admin.mailing.index') }}">
                                            Mailing
                                        </a>
                                    </div>
                                </li>
                            @endcan
                            <li class="nav-item dropdown messagesdd">
                                <notifications
                                    :all-notifications="{{ $notifications }}"
                                    :user="{{ Auth::user() }}"
                                ></notifications>
                            </li>
                            <li class="nav-item dropdown persondd">
                                <a id="personDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fa fa-user-o" aria-hidden="true"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="personDropdown" id="userDD" data-user-id="{{ Auth::user()->id }}">
                                    <p class="hi dropdown-item disabled">Hi, <span class="">{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</span></p>

                                    @can('provider-account-details')
                                        <a class="dropdown-item" href="{{ route('account-details') }}">
                                            Account Details
                                        </a>
                                        <a class="dropdown-item" href="{{ route('provider.edit.getPaid') }}">
                                            Payment Info
                                        </a>
                                        <a class="dropdown-item" href="{{ route('provider.onboarding.photo') }}">
                                            Provider Details
                                        </a>
                                        <provider :user="{{ Auth::user() }}" update-location-url="{{ route('updateLocation') }}"></provider>
                                    @endcan
                                    @can('practice-details')
                                        <a class="dropdown-item" href="{{ route('practice.details.base') }}">
                                            Practice Details
                                        </a>
                                        <practice :user="{{ Auth::user() }}"></practice>
                                    @endcan
                                    @can('can-hire')
                                        <a class="dropdown-item" href="{{ route('shifts.index') }}">
                                            Hire Providers
                                        </a>
                                    @endcan
                                    @can('provider-shift')
                                        <a class="dropdown-item" href="{{ route('shifts.provider.index') }}">
                                            My jobs
                                        </a>
                                    @endcan
                                    @can('can-referral')
                                        <a class="dropdown-item" href="{{ route('referral.index') }}">
                                            Referral program
                                        </a>
                                    @endcan
                                    @can('provider-account-details')
                                        <a class="dropdown-item" href="{{ route('my-licenses') }}">
                                            Licenses
                                        </a>
                                        <a class="dropdown-item" href="{{ route('provider.edit.identity') }}">
                                            Identity
                                        </a>
                                    @endcan
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); sessionStorage.clear(); document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main>
            @include('layouts._betaAlert')
            @include('layouts._errors')
            @yield('content')
        </main>
    </div>
    @can('admin-panel')
        <script src="{{ mix('js/admin.js', 'build') }}" defer></script>
    @else
        <script src="{{ mix('js/app.js', 'build') }}" defer></script>
    @endcan
    @stack('custom-scripts')
</body>
</html>
