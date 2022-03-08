@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Create Account',
                    'desc' => 'Please enter your account Information.'
                ])
                <user-base
                    inline-template
                    provider-url="{{ route('signup.userBaseSave') }}"
                    practice-url="{{ route('practice.signup.userBaseSave') }}"
                    partner-url="{{ route('base.signup.userBaseSave') }}"
                    init-type="{{ old('provider-c', $type == 'provider') ? 'provider' : (old('practice-c', $type == 'practice') ? 'practice' : (old('partner-c', $type == 'partner') ? 'partner' : ''))  }}"
                    auto-save-action="{{ route('signup.autoSave') }}"
                    terms-url="{{ route('terms') }}"
                    v-cloak
                >
                    <div>
                        <form method="POST" :action="action">
                            @csrf

                            @if (!$type)
                                <div class="row">
                                    <div class="col-6 col-sm-6">
                                        <div class="user-type" :class="{'active' : isProvider()}" @click="selectUserType('provider')" data-container="body" data-toggle="popover" data-placement="top" data-content="Providers are Dentists, Hygienists, Assistants, and Front Office looking for temporary work." data-trigger="hover">
                                            <div class="custom-control custom-checkbox">
                                                <input class="form-check-input custom-control-input" type="checkbox" id="user-type" name="provider-c" v-model="providerCheckbox" @change="selectUserType('provider')">
                                                <label for="user-type" class="custom-control-label"></label>
                                            </div>
                                            <div class="cont">
                                                <i class="fa fa-user-md"></i>
                                                <p>Provider</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-sm-6">
                                        <div class="user-type" :class="{'active' : isPractice()}" @click="selectUserType('practice')" data-container="body" data-toggle="popover" data-placement="top" data-content="Practices use Boon to hire temporary team members." data-trigger="hover">
                                            <div class="custom-control custom-checkbox">
                                                <input class="form-check-input custom-control-input" type="checkbox" id="user-type-p" name="practice-c" v-model="practiceCheckbox" @change="selectUserType('practice')">
                                                <label for="user-type-p" class="custom-control-label"></label>
                                            </div>
                                            <div class="cont">
                                                <i class="fa fa-hospital-o"></i>
                                                <p>Practice</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <span class="invalid-feedback" role="alert" v-if="showUserTypeError">
                            <strong><i class="fa fa-exclamation-circle"></i> Select user type</strong>
                        </span>

                            <div class="row">
                                <div class="col-md-6">
                                    <Cinput
                                        label="First Name"
                                        id="first_name"
                                        type="text"
                                        name="first_name"
                                        value="{{ old('first_name') }}"
                                        has-errors="{{ $errors->has('first_name') }}"
                                        first-error="{{ $errors->first('first_name') }}"
                                        :required="false"
                                        :is-mat="true"
                                        :prepend="true"
                                        prepend-icon="user-o"
                                        :init-model="first_name"
                                        init-model-attr="first_name"
                                    ></Cinput>
                                </div>
                                <div class="col-md-6">
                                    <Cinput
                                        label="Last Name"
                                        id="last_name"
                                        type="text"
                                        name="last_name"
                                        value="{{ old('last_name') }}"
                                        has-errors="{{ $errors->has('last_name') }}"
                                        first-error="{{ $errors->first('last_name') }}"
                                        :required="false"
                                        :is-mat="true"
                                        :prepend="true"
                                        prepend-icon="user-o"
                                        :init-model="last_name"
                                        init-model-attr="last_name"
                                    ></Cinput>
                                </div>
                            </div>

                            <Cinput
                                label="E-mail Address"
                                id="email"
                                type="text"
                                name="email"
                                value="{{ old('email') }}"
                                has-errors="{{ $errors->has('email') }}"
                                first-error="{{ $errors->first('email') }}"
                                :required="false"
                                :is-mat="true"
                                :prepend="true"
                                prepend-icon="envelope-o"
                                :init-model="email"
                                init-model-attr="email"
                                @blur-input="blurEmail()"
                            ></Cinput>

                            <input type="hidden" name="industry" value="{{ $industry }}">
                            <input type="hidden" name="code" value="{{ $code }}">
                            <input type="hidden" name="lat" v-model="lat">
                            <input type="hidden" name="lng" v-model="lng">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input class="form-check-input custom-control-input" type="checkbox" name="accept" id="accept" v-model="accept" {{ old('accept') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="accept">I agree to the</label> <a id="termsmodal" href="#" @click.prevent="" class="boon-link" data-toggle="modal" data-target="#termsModal">terms and conditions</a>
                                </div>
                                <span class="invalid-feedback" role="alert" v-if="showTermError">
                                <strong><i class="fa fa-exclamation-circle"></i>You must read and agree to terms to proceed.</strong>
                            </span>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn form-button" @click="submit($event)">Create Account</button>
                                <p class="after-button">Already have an account? <a class="boon-link" href="{{ route('login') }}">Sign In</a></p>
                            </div>
                        </form>

                        <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        {{--<h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>--}}
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        {{--<p>By accessing or using the Service you agree to be bound by these Terms. If you disagree with any part of the terms then you may not access the Service.</p>
                                        <a class="boon-link" target="_blank" href="{{ url('/terms-of-service') }}">Terms and Conditions</a>
                                        --}}
                                        <div v-html="termsHtml"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </user-base>
            </div>
        </div>
    </div>

@endsection
