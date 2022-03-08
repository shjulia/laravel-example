@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form auth-long-form">
                @include('partials._form-titles', [
                    'h1' => 'Create Account',
                    'desc' => "Sign-up is quick and easy. It's take you less a commercial break complete and then you'll be one step closer to earning money by practicing good. Here are a few items we will ask you for, so please have them ready."
                ])
                <div class="card details needs">
                    <div class="card-body">
                        {{--<h1>Create Account</h1>
                        <div class="text-center">
                            <h2>Here's What You Need</h2>
                            <h3>Sign-up is quick and easy</h3>
                            <p class="h1_subtitle">It's take you less a commercial break complete and then you'll be one step closer to earning money by practicing good. Here are a few items we will ask you for, so please have them ready.</p>
                        </div>--}}

                        <div class="row row-need">
                            <div class="col-md-3 text-center">
                                <i class="fa fa-id-card-o icon blue"></i>
                            </div>
                            <div class="col-md-9">
                                <span class="title">Driver's license</span>
                                <div class="pull-right">
                                    <div class="custom-control custom-checkbox big-checkbox">
                                        <input type="checkbox" {{ in_array($user->signup_step, ['provider:check', 'provider:license']) ? 'checked' : '' }} disabled class="custom-control-input" id="driverCheck">
                                        <label class="custom-control-label" for="driverCheck"></label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="desc">This helps verify your identity so we can get you paid</p>
                            </div>
                        </div>
                        <div class="row row-need">
                            <div class="col-md-3 text-center">
                                <i class="fa fa-list-alt icon orange"></i>
                            </div>
                            <div class="col-md-9">
                                <span class="title">Professional License(s)</span>
                                <div class="pull-right">
                                    <div class="custom-control custom-checkbox big-checkbox">
                                        <input type="checkbox" {{ $user->signup_step == 'provider:check' ? 'checked' : '' }} disabled class="custom-control-input" id="driverCheck">
                                        <label class="custom-control-label" for="driverCheck"></label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="desc">We need to hae your clinical license on file for the safety of your patients.</p>
                            </div>
                        </div>
                        <div class="row row-need">
                            <div class="col-md-3 text-center">
                                <i class="fa fa-id-card-o icon grey"></i>
                            </div>
                            <div class="col-md-9">
                                <span class="title">Social Security Information</span>
                                <div class="pull-right">
                                    <div class="custom-control custom-checkbox big-checkbox">
                                        <input type="checkbox" disabled class="custom-control-input" id="driverCheck">
                                        <label class="custom-control-label" for="driverCheck"></label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="desc">This id the final step to make sure you get paid and we will run quick background check. Don't worry this is super secure.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route($routeBase, ['code' => $user->tmp_token]) }}" class="btn form-button">Continue</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


    {{--<div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-6">
                <div class="card details needs">
                    <div class="card-body">
                        <h1>Create Account</h1>
                        <div class="text-center">
                            <h2>Here's What You Need</h2>
                            <h3>Sign-up is quick and easy</h3>
                            <p class="h1_subtitle">It's take you less a commercial break complete and then you'll be one step closer to earning money by practicing good. Here are a few items we will ask you for, so please have them ready.</p>
                        </div>

                        <div class="row row-need">
                            <div class="col-md-3 text-center">
                                <i class="fa fa-id-card-o icon blue"></i>
                            </div>
                            <div class="col-md-9">
                                <span class="title">Driver's license</span>
                                <div class="pull-right">
                                    <div class="custom-control custom-checkbox big-checkbox">
                                        <input type="checkbox" {{ in_array($user->signup_step, ['provider:check', 'provider:license']) ? 'checked' : '' }} disabled class="custom-control-input" id="driverCheck">
                                        <label class="custom-control-label" for="driverCheck"></label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="desc">This helps verify your identity so we can get you paid</p>
                            </div>
                        </div>
                        <div class="row row-need">
                            <div class="col-md-3 text-center">
                                <i class="fa fa-list-alt icon orange"></i>
                            </div>
                            <div class="col-md-9">
                                <span class="title">Professional License(s)</span>
                                <div class="pull-right">
                                    <div class="custom-control custom-checkbox big-checkbox">
                                        <input type="checkbox" {{ $user->signup_step == 'provider:check' ? 'checked' : '' }} disabled class="custom-control-input" id="driverCheck">
                                        <label class="custom-control-label" for="driverCheck"></label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="desc">We need to hae your clinical license on file for the safety of your patients.</p>
                            </div>
                        </div>
                        <div class="row row-need">
                            <div class="col-md-3 text-center">
                                <i class="fa fa-id-card-o icon grey"></i>
                            </div>
                            <div class="col-md-9">
                                <span class="title">Social Security Information</span>
                                <div class="pull-right">
                                    <div class="custom-control custom-checkbox big-checkbox">
                                        <input type="checkbox" disabled class="custom-control-input" id="driverCheck">
                                        <label class="custom-control-label" for="driverCheck"></label>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <p class="desc">This id the final step to make sure you get paid and we will run quick background check. Don't worry this is super secure.</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <a href="{{ route($routeBase, ['code' => $user->tmp_token]) }}" class="btn form-button">Continue</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection--}}
