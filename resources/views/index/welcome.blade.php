<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Boon</title>
    <link href="{{ mix('css/app.css', 'build') }}" rel="stylesheet">
    <meta name="apple-itunes-app" content="app-id=1471640517, app-argument={{ config('app.url') }}">
    @laravelPWA
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
</head>
<body class="grey">
<div class="flex-center position-ref full-height" id="app">
    <div class="text-right welcome-links">
        @auth
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        @endauth
    </div>

    <div class="content">
        <div class="logo-div">
            <a class="" href="{{ route('index') }}"><img src="{{ asset('/img/boon-logo.svg') }}"></a>
        </div>
        @guest
            <wellcome
                inline-template
                provider-url="{{ route('signup.userBaseDirect', ['type' => 'provider']) }}"
                practice-url="{{ route('signup.userBaseDirect', ['type' => 'practice']) }}"
                partner-url="{{ route('signup.userBaseDirect', ['type' => 'partner']) }}"
            >
                <div class="container wellcome">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="signup-type"
                                 :class="{'active' : isProvider()}" @click="selectUserType('provider')"
                            >
                                <div class="cont">
                                    <img src="{{ asset('/img/signup-earn.png') }}" alt="earn"/>
                                    <p>
                                        <span class="title">Earn</span>
                                        <span class="desc">Providers are Dentists, Hygienists, Assistants, and Front Office looking for temporary work.</span>
                                    </p>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input
                                        class="form-check-input custom-control-input" type="checkbox" id="user-type-pro" name="provider"
                                        v-model="providerCheckbox" @change="selectUserType('provider')"
                                    >
                                    <label for="user-type-pro" class="custom-control-label"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="signup-type"
                                 :class="{'active' : isPractice()}" @click="selectUserType('practice')"
                            >
                                <div class="cont">
                                    <img src="{{ asset('/img/signup-hire.png') }}" alt="hire"/>
                                    <p>
                                        <span class="title">Hire</span>
                                        <span class="desc">Practices use Boon to hire temporary team members.</span>
                                    </p>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input
                                        class="form-check-input custom-control-input" type="checkbox" id="user-type-pra" name="practice"
                                        v-model="practiceCheckbox" @change="selectUserType('practice')"
                                    >
                                    <label for="user-type-pra" class="custom-control-label"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6">
                            <div class="signup-type"
                                 :class="{'active' : isPartner()}" @click="selectUserType('partner')"
                            >
                                <div class="cont">
                                    <img src="{{ asset('/img/signup-refer.png') }}" alt="refer"/>
                                    <p>
                                        <span class="title">Refer</span>
                                        <span class="desc">Not a Practice or Provider? Earn extra money by referring people to sign-up for Boon.</span>
                                    </p>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input
                                        class="form-check-input custom-control-input" type="checkbox" id="user-type-pa" name="partner"
                                        v-model="partnerCheckbox" @change="selectUserType('partner')"
                                    >
                                    <label for="user-type-pa" class="custom-control-label"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <div v-cloak>
                                <span class="invalid-feedback" role="alert" v-if="showUserTypeError">
                                    <strong><i class="fa fa-exclamation-circle"></i> You should select user type.</strong>
                                </span>
                                <br/>
                                <a :href="action" class="btn form-button" @click="submit($event)">Create Account</a>
                            </div>
                            <p class="havacc">Have an account? <a href="{{ route('login') }}">Login</a></p>
                        </div>
                    </div>
                </div>
            </wellcome>
        @endguest
    </div>
</div>
<script src="{{ mix('js/registration.js', 'build') }}" defer></script>
</body>
</html>
