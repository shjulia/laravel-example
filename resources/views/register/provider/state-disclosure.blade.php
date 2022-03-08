@extends('layouts.auth')

@section('content')
    <div class="wraper wraper-login">
        <div class="auth-container">
            <div class="auth-form step-user-base">
                @include('partials._form-titles', [
                    'h1' => 'California Disclosure',
                    'desc' => '',
                    'backUrl' => route('signup.check', ['code' => $user->tmp_token]),
                ])

                @include("register.provider._stepper", [
                    'active' => 'check'
                ])
                <disclosure-step
                    inline-template
                >
                    <div>
                        <div class="scrollable">
                            <p><b>NOTICE REGARDING BACKGROUND CHECKS PER CALIFORNIA LAW</b></p>
                            <p>Boon (the “Company”) intends to obtain information about you for employment
                            screening purposes from a consumer reporting agency.  Thus, you can expect to be
                            the subject of an “investigative consumer report” and a “consumer credit report”
                            obtained for employment purposes.  Such reports may include information about your
                            character, general reputation, personal characteristics and mode of living.  With
                            respect to any investigative consumer report from an investigative consumer reporting
                            agency (“ICRA”), the Company may investigate the information contained in your employment
                            application and other background information about you, including but not limited to
                            obtaining a criminal record report, verifying references, work history, your social
                            security number, your educational achievements, licensure, and certifications, your
                            driving record, and other information about you, and interviewing people who are
                            knowledgeable about you.  The results of this report may be used as a factor in making
                            employment decisions.  The source of any investigative consumer report (as that term
                            is defined under California law) will be <strong>Checkr, Inc., One Montgomery Street, Suite
                            2400, San Francisco, CA 94104 | (844) 824-3257 |
                            <a href="https://candidate.checkr.com" target="_blank">https://candidate.checkr.com</a>.</strong>
                            The Company agrees to provide you with a copy of an investigative consumer report when
                            required to do so under California law.</p>

                            <p>Under California Civil Code section 1786.22, you are entitled to find out what is in the
                            CRA’s file on you with proper identification, as follows:</p>

                            <ul>
                              <li>In person, by visual inspection of your file during normal business hours and on reasonable notice. You also may request a copy of the information in person. The CRA may not charge you more than the actual copying costs for providing you with a copy of your file.</li>
                              <li>A summary of all information contained in the CRA file on you that is required to be provided by the California Civil Code will be provided to you via telephone, if you have made a written request, with proper identification, for telephone disclosure, and the toll charge, if any, for the telephone call is prepaid by or charged directly to you.</li>
                              <li>By requesting a copy be sent to a specified addressee by certified mail. CRAs complying with requests for certified mailings shall not be liable for disclosures to third parties caused by mishandling of mail after such mailings leave the CRAs.</li>
                            </ul>

                            <p>“Proper Identification” includes documents such as a valid driver’s license, social
                            security account number, military identification card, and credit cards. Only if you
                            cannot identify yourself with such information may the CRA require additional information
                            concerning your employment and personal or family history in order to verify your identity.</p>

                            <p>The CRA will provide trained personnel to explain any information furnished to you and will
                            provide a written explanation of any coded information contained in files maintained on you.
                            This written explanation will be provided whenever a file is provided to you for visual
                            inspection. You may be accompanied by one other person of your choosing, who must furnish
                            reasonable identification. An CRA may require you to furnish a written statement granting
                            permission to the CRA to discuss your file in such person’s presence.</p>
                        </div>
                        <form method="POST" action="{{ route('signup.stateDisclosure', ['code' => $user->tmp_token]) }}" v-cloak>
                            @csrf
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="form-check-input custom-control-input" v-model="accept" id="accept" name="accept">
                                    <label class="custom-control-label" for="accept">I acknowledge receipt of the California Disclosure and certify that I have read and understand this document.</label>
                                </div>
                                <span class="invalid-feedback" role="alert" v-if="showAcceptError">
                                    <strong><i class="fa fa-exclamation-circle"></i>You must read and agree to disclosure.</strong>
                                </span>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="form-check-input custom-control-input" name="copy" id="copy">
                                    <label class="custom-control-label" for="copy">Please check this box if you would like to receive a copy of a consumer report.</label>
                                </div>
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
