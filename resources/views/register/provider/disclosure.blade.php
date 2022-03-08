@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Disclosure Regarding Background Investigation',
                    'desc' => '',
                    'backUrl' => route('signup.check', ['code' => $user->tmp_token]),
                    'h1Class' => 'h1_little'
                ])

                @include("register.provider._stepper", [
                    'active' => 'check'
                ])
                <disclosure-step
                    inline-template
                >
                    <div>
                        <div class="scrollable">
                            <p>Boon (the “Company”) may obtain information about you from a third party consumer
                            reporting agency for employment purposes. Thus, you may be the subject of a “consumer report”
                            which may include information about your character, general reputation, personal characteristics,
                            and/or mode of living.  These reports may contain information regarding your credit history,
                            criminal history, motor vehicle records (“driving records”), verification of your education or
                            employment history, or other background checks.</p>

                            <p>You have the right, upon written request made within a reasonable time, to request whether a
                            consumer report has been run about you and to request a copy of your report.  These searches will
                            be conducted by <strong>Checkr, Inc., One Montgomery Street, Suite 2400, San Francisco, CA 94104 |
                            (844) 824-3257 | <a href="https://candidate.checkr.com" target="_blank">https://candidate.checkr.com</a>.</strong></p>
                        </div>
                        <form method="POST" action="{{ route('signup.disclosure', ['code' => $user->tmp_token]) }}" v-cloak>
                            @csrf
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="form-check-input custom-control-input" v-model="accept" id="accept" name="accept">
                                    <label class="custom-control-label" for="accept">I acknowledge receipt of the Disclosure Regarding Background Investigation and certify that I have read and understand this document.</label>
                                </div>
                                <span class="invalid-feedback" role="alert" v-if="showAcceptError">
                                <strong><i class="fa fa-exclamation-circle"></i>You must read and agree to disclosure regarding background investigation to proceed.</strong>
                            </span>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn form-button" @click="submit($event)">Continue</button>
                            </div>
                        </form>
                    </div>
                </disclosure-step>
            </div>
        </div>
    </div>
@endsection
