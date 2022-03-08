You're almost ready to start practicing good with boon
It looks like there are still a few more items we need to process your account.

We take the security very seriously. We handle all of your information with bank-level encryption.
We simply require some of these items for the safety of patients and so that we can make sure you
get paid :)

Please add the following information:
@if($user->signup_step == 'provider:industry')
    -Industry
    - Driver's license
    - Professional license(s)
    - Social Security Information
@elseif($user->signup_step == 'provider:identity')
    - Driver's license
    - Professional license(s)
    - Social Security Information
@elseif($user->signup_step == 'provider:license')
    - Professional license(s)
    - Social Security Information
@elseif($user->signup_step == 'provider:check')
    - Social Security Information
@endif

@if (Route::has('signup.' . explode(':', $user->signup_step)[1]))
Continue sign up: {{ route('signup.' . explode(':', $user->signup_step)[1], $user->tmp_token) }}
@endif
