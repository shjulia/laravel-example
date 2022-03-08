@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'Acknowledgment and Authorization for Background Check',
                    'desc' => '',
                    'backUrl' => route('signup.disclosure', ['code' => $user->tmp_token]),
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
                            <p>I acknowledge receipt of the separate documents entitled Disclosure Regarding Background
                            Investigation and A Summary of Your Rights Under the Fair Credit Reporting Act and certify that I
                            have read and understand both of those documents. I hereby authorize the obtaining of “consumer
                            reports” and/or “investigative consumer reports” by the Boon (the “Company”) at any time
                            after receipt of this authorization and throughout my employment, if applicable.  To this end, I
                            hereby authorize any law enforcement agency, administrator, state or federal agency, institution,
                            school or university (public or private), information service bureau, past or present employers,
                            motor vehicle records agencies, or insurance company to furnish any and all background information
                            requested by <strong>Checkr, Inc., One Montgomery Street, Suite 2400,  San Francisco, CA 94104
                            | (844) 824-3257 |
                            <a href="https://candidate.checkr.com" target="_blank">https://candidate.checkr.com</a></strong> and/or
                            the Company.  I agree that a facsimile (“fax”), electronic, or photographic copy of this
                            Authorization shall be as valid as the original.</p>

                            <p><strong>New York residents/candidates only:</strong>  Upon request, you will be informed whether or not a
                            consumer report was requested by the Employer, and if such report was requested, informed of the
                            name and address of the consumer reporting agency that furnished the report.   You have the right
                            to inspect and receive a copy of any investigative consumer report requested by the Employer by
                            contacting the consumer reporting agency identified above directly. By signing below, you
                            acknowledge receipt of Article 23-A of the New York Correction Law. <a href="https://www.labor.ny.gov/formsdocs/wp/correction-law-article-23a.pdf" target="_blank">Link to NY Article 23-A</a></p>

                            <p><strong>New York City residents/candidates only:</strong>  You acknowledge and authorize the Employer to
                            provide any notices required by federal, state or local law to you at the address(es) and/or email
                            address(es) you provided to the Employer.</p>

                            <p><strong>Washington State candidates only:</strong>  You also have the right to request from the consumer
                            reporting agency a written summary of your rights and remedies under the Washington Fair Credit
                            Reporting Act.</p>

                            <p><strong>Minnesota and Oklahoma candidates only:</strong>  Please check the box below if you would like to
                            receive a copy of a consumer report if one is obtained by the Company.</p>

                            <p><strong>San Francisco candidates only:</strong>  Please click below for the San Francisco Fair Chance Act Notice.
                            - <a href="https://s3.amazonaws.com/checkr/public/SFFairChanceNotice.pdf" target="_blank">English</a>
                            - <a href="https://s3.amazonaws.com/checkr/public/SFOrdinance-Spanish.pdf" target="_blank">Spanish</a>
                            - <a href="https://s3.amazonaws.com/checkr/public/SFOrdinance-Tagalog.pdf" target="_blank">Tagalog</a>
                            - <a href="https://s3.amazonaws.com/checkr/public/SFOrdinance-Chinese.pdf" target="_blank">Chinese</a></p>

                            <p><strong>Los Angeles candidates only:</strong>  Please click <a href="https://bca.lacity.org/Uploads/fciho/Notice%20to%20Applicants%20and%20Employees%20for%20Private%20Employers.pdf">here</a> for the Los Angeles Notice to Candidates and Employees for Private Employers.</p>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="form-check-input custom-control-input" v-model="accept" id="accept" name="accept">
                                <label class="custom-control-label" for="accept">Please check this box if you would like to receive a copy of a consumer report.</label>
                            </div>
                        </div>

                        <h4>Electronic signature</h4>
                        <p><b>By typing my name below, I consent to the background checks and indicate my agreement to all of the above.</b></p>

                        <form method="POST" action="{{ route('signup.authorization', ['code' => $user->tmp_token]) }}" v-cloak>
                            @csrf
                            <Cinput
                                label="Full name"
                                id="full_name"
                                type="text"
                                name="full_name"
                                value="{{ old('full_name') }}"
                                has-errors="{{ $errors->has('full_name') }}"
                                first-error="{{ $errors->first('full_name') }}"
                                :required="false"
                                :is-mat="true"
                            ></Cinput>
                            <div class="form-group">
                                <div class="g-recaptcha"
                                     data-sitekey="{{ $reCaptchaKey }}">
                                </div>
                            </div>
                            @if($errors->has('g-recaptcha-response'))
                                <span class="invalid-feedback" role="alert">
                                    <strong><i class="fa fa-exclamation-circle"></i> {{ $errors->first('g-recaptcha-response') }}</strong>
                                </span>
                            @endif
                            <div class="form-group">
                                <button type="submit" class="btn form-button">Continue</button>
                            </div>
                        </form>
                    </div>
                </disclosure-step>
            </div>
        </div>
    </div>
@endsection
@push('custom-scripts')
    <script src="https://www.google.com/recaptcha/api.js"></script>
@endpush
