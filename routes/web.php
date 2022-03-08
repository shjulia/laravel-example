<?php

require_once 'mailgun.php';

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/save-push', 'PushController@store');
Route::get('/', 'IndexController@welcome')->name('index');
Auth::routes();
Route::post('stripe/webhook/status', 'Background\StripeWebhookController@chargeStatus');
Route::post('webhook/update-balance', 'Background\StripeWebhookController@updateBalance');
Route::get('/set-password/{token}', 'Auth\SetPasswordController@showForm')->name('set-password-form');
Route::post('/set-password', 'Auth\SetPasswordController@save')->name('set-password-save');
Route::get('/privacy-policy', function() {
    return response()->redirectTo('https://boonb.s3.amazonaws.com/prod/docs/Boon_Privacy_Policy_053019_v1.docx');
});
/*Route::get('/terms-of-service', function() {
    return response()->redirectTo('https://boonb.s3.amazonaws.com/prod/docs/Boon-Terms-of-Service.docx');
});*/
Route::get('/terms-of-service', 'StaticData\StaticController@terms');
Route::get('/terms', 'StaticData\StaticController@terms')->name('terms');
Route::get('/privacy', 'StaticData\StaticController@privacy')->name('privacy');
Route::get('/fcra', 'StaticData\StaticController@fcra')->name('fcra');

Route::post('/deactivate-account/{user}', 'Auth\ActivationController@deactivate')->name('deactivate-account');
Route::post('/activate-account/{user}', 'Auth\ActivationController@activate')->name('activate-account');

Route::group([
    'middleware' => ['auth', 'App\Http\Middleware\Auth\FinishSignup']
], function () {
    Route::post('/notification/mark-as-read', 'NotificationController@markAsRead');
    Route::post('/set-time-diff', 'HomeController@setTimeDifference')->name('setTimeDiff');
    Route::post('/subscribe-to-push', 'HomeController@subscribeToPush');
    Route::group([
        'middleware' => ['App\Http\Middleware\Allow']
    ], function () {
        Route::get('/home', 'HomeController@index')->name('home');

        Route::group([
            'prefix' => 'shifts',
            'as' => 'shifts.',
            'namespace' => 'Shift\Practice',
            'middleware' => ['can:can-hire', 'isAccountActive']
        ],
            function () {
                Route::get('/', 'ShiftController@index')->name('index');
                    Route::get('/base/{shift?}', 'ShiftController@base')->name('base');
                    Route::post('/create-base/{shift?}', 'ShiftController@createBase')->name('createBase');
                    Route::get('/location/{shift}', 'ShiftController@location')->name('location');
                    Route::post('/location/{shift}', 'ShiftController@setLocation')->name('location');
                    Route::get('/time/{shift}', 'ShiftController@time')->name('time');
                    Route::post('/time/{shift}', 'ShiftController@setTime')->name('setTime');
                    Route::get('/tasks/{shift}', 'ShiftController@tasks')->name('tasks');
                    Route::post('/tasks/{shift}', 'ShiftController@setTasks')->name('setTasks');
                Route::get('/result/{shift}', 'ShiftController@result')->name('result');
                Route::post('/cancel/{shift}', 'ShiftController@cancel')->name('cancel');
                Route::post('/coupon/{shift}', 'ShiftController@coupon')->name('coupon');
                Route::get('/check-changes/{shift}', 'ShiftController@checkChanges')->name('checkChanges');
                Route::get('/details/{shift}', 'ShiftController@details')->name('details');
                Route::get('/multiple-details/{shift}', 'ShiftController@multipleDetails')->name('multipleDetails');
                Route::post('/find-new/{shift}', 'ShiftController@findNewProvider')->name('find-new');
                Route::post('/finish/{shift}', 'ShiftController@finish')->name('finish');

                Route::group([
                    'prefix' => '{shift}/reviews',
                    'as' => 'reviews.',
                ],
                    function () {
                        Route::get('/', 'ReviewController@review')->name('review');
                        Route::post('/create', 'ReviewController@createReviewToProvider')->name('createReview');
                    });
            }
        );
        Route::get('/shifts/{shift}/reviews/watch', 'Shift\ShowReviewController@watchReviewToProvider')->name('shifts.reviews.watchReviewToProvider');
        Route::get('/shifts/provider/{shift}/reviews/watch', 'Shift\ShowReviewController@watchReviewToPractice')->name('shifts.reviews.watchReviewToPractice');

        Route::post('/update-location', 'Background\LocationController@updateLocation')->name('updateLocation');
        Route::group([
            'prefix' => 'shifts/provider',
            'as' => 'shifts.provider.',
            'namespace' => 'Shift\Provider',
            'middleware' => ['can:provider-shift', 'isAccountActive']
        ],
            function () {
                Route::get('/', 'ShiftController@index')->name('index');
                Route::post('/accept/{shift}', 'ShiftController@accept')->name('accept');
                Route::post('/multiple-accept/{shift}', 'ShiftController@multipleAccept')->name('multipleAccept');
                Route::get('/decline/{shift}', 'ShiftController@decline')->name('decline');
                Route::post('/view-invite/{shift}', 'ShiftController@viewInvite')->name('viewInvite');
                Route::get('/available', 'ShiftController@available')->name('available');
                Route::get('/result-show/{shift}', 'ShiftController@showResult')->name('resultShow');
                Route::post('/withdraw', 'PaymentsController@withdraw')->name('withdraw');
                Route::get('info/{shift?}', 'ShiftController@index')->name('info');
                Route::post('/start/{shift}', 'ShiftController@start')->name('start');
                Route::post('/finish/{shift}', 'ShiftController@finish')->name('finish');

                Route::group([
                    'prefix' => '{shift?}/reviews',
                    'as' => 'reviews.',
                ],
                    function () {
                        Route::get('/', 'ReviewController@review')->name('review');
                        Route::post('/create', 'ReviewController@createReviewToProvider')->name('createReview');
                    });

                Route::get('/{shift}', 'ShiftController@acceptPage')->name('acceptPage');
            }
        );
    });
    Route::get('/dashboard', 'HomeController@dashboard')->name('dashboard');
    Route::get('/not-allow', 'HomeController@notAllow')->name('notAllow');

    Route::get('/account-detail', 'Auth\Provider\DetailsController@showForm')->name('account-details');
    Route::get('/my-licenses', 'Auth\Provider\LicensesController@showForm')->name('my-licenses');
    Route::post('/license/create', 'Auth\Provider\LicensesController@create')->name('license.create');
    Route::post('/one-license', 'Auth\Provider\LicensesController@oneLicenseSave')->name('one-license.save');
    Route::delete('/licence/remove', 'Auth\Provider\LicensesController@removeLicense')->name('license.remove');
    Route::post('/save-photo', 'Auth\Provider\DetailsController@savePhoto')->name('savePhoto');
    Route::post('/save-details', 'Auth\Provider\DetailsController@store')->name('saveDetails');
    Route::get('/autocomplete/{query?}/{lat?}/{lng?}', 'Auth\Provider\DetailsController@autocomplete')->name('details.autocomplete');
    Route::get('/place-data/{placeId?}', 'Auth\Provider\DetailsController@placeData')->name('details.placeData');
    Route::post('/upload-medical', 'Auth\Provider\LicensesController@uploadMedicalLicense')->name('upload.Medical');

    Route::group([
        'prefix' => 'provider/onboarding',
        'as' => 'provider.onboarding.',
        'namespace' => 'Auth\Provider',
        'middleware' => 'can:provider-account-details'
    ],
        function () {
            Route::get('/photo', 'OnboardingController@photo')->name('photo');
            Route::post('/photo', 'OnboardingController@photoNext')->name('photoNext');
            Route::get('/shift-length', 'OnboardingController@shiftLength')->name('shiftLength');
            Route::post('/shift-length', 'OnboardingController@shiftLengthSave')->name('shiftLength');
            Route::get('/distance', 'OnboardingController@distance')->name('distance');
            Route::post('/distance', 'OnboardingController@distanceSave')->name('distance');
            Route::get('/rate', 'OnboardingController@rate')->name('rate');
            Route::post('/rate', 'OnboardingController@rateSave')->name('rate');
            Route::get('/tool', 'OnboardingController@tool')->name('tool');
            Route::post('/tool', 'OnboardingController@savetool')->name('toolSave');
            Route::get('/tasks', 'OnboardingController@tasks')->name('tasks');
            Route::post('/tasks', 'OnboardingController@tasksSave')->name('tasks');
            Route::get('/specialities', 'OnboardingController@specialities')->name('specialities');
            Route::post('/specialities', 'OnboardingController@specialitiesSave')->name('specialities');

            Route::get('/availability', 'OnboardingController@availability')->name('availability');
            Route::post('/availability', 'OnboardingController@availabilitySave')->name('availability');

            Route::get('/holidays', 'OnboardingController@holidays')->name('holidays');
            Route::post('/holidays', 'OnboardingController@holidaysAvailabilitySave')->name('holidays');
        }
    );

    Route::group([
        'prefix' => 'provider/edit',
        'as' => 'provider.edit.',
        'namespace' => 'Auth\Provider',
    ],
        function () {
            Route::post('/phone', 'EditController@phone')->name('phoneSave');
            Route::get('/identity', 'EditController@identity')->name('identity');
            Route::post('/identity', 'EditController@identitySave')->name('identitySave');
            Route::post('/upload-driver', 'EditController@uploadDriverLicense')->name('uploadDriver');
            Route::delete('/identity', 'EditController@identityDelete')->name('identity');

            Route::get('/get-paid', 'PaymentController@getPaid')->name('getPaid');
            Route::post('/get-paid', 'PaymentController@getPaidSave')->name('getPaid');
        }
    );

    Route::group([
        'prefix' => 'practice/details',
        'as' => 'practice.details.',
        'namespace' => 'Auth\Practice',
    ],
        function () {
            Route::get('/', 'DetailsController@baseDetails')->name('base');
            Route::post('/save-photo', 'DetailsController@savePhoto')->name('savePhoto');
            Route::post('/save-practice-details', 'DetailsController@saveBaseDetails')->name('practiceSaveDetails');
            Route::get('/secondary', 'DetailsController@secondaryDetails')->name('secondary');
            Route::post('/secondary', 'DetailsController@saveSecondaryDetails')->name('secondarySave');
            Route::get('/tool', 'DetailsController@tool')->name('tool');
            Route::post('/tool', 'DetailsController@savetool')->name('toolSave');

            Route::get('/locations', 'DetailsController@locations')->name('locations');
            Route::post('/locations', 'DetailsController@addLocation')->name('locations.create');
            Route::post('/locations-current', 'DetailsController@editCurrentLocation')->name('locations.edit-current');
            Route::post('/locations/{practiceAddress}', 'DetailsController@editLocation')->name('locations.edit');
            Route::delete('/locations/{practiceAddress}', 'DetailsController@removeLocation')->name('locations.delete');

            Route::get('/team', 'DetailsController@team')->name('team');
            Route::post('/team', 'DetailsController@teamSave')->name('teamSave');
            Route::delete('/team', 'DetailsController@deleteMember')->name('deleteMember');
            Route::get('/billing', 'DetailsController@billing')->name('billing');
            Route::post('/save-billing', 'DetailsController@billingSave')->name('billingSave');
            Route::get('/success', 'DetailsController@success')->name('success');
        }
    );
    Route::group([
        'prefix' => 'referral',
        'as' => 'referral.',
        'namespace' => 'Referral',
        'middleware' => 'can:can-referral'
    ],
        function () {
            Route::get('/', 'ReferralController@index')->name('index');
            Route::post('/invite', 'ReferralController@invite')->name('invite');
            Route::post('/reinvite/{invite}', 'ReferralController@reinvite')->name('reinvite');
            Route::get('/invites', 'ReferralController@invites')->name('invites');
            Route::post('/code', 'ReferralController@changeCode')->name('code');
        }
    );
});
Route::get('/what-you-need/{code}', 'Auth\Provider\NeedController@need')->name('need');

Route::group([
        'prefix' => 'sign-up',
        'as' => 'signup.',
        'namespace' => 'Auth\Provider',
    ],
    function () {
        //Route::get('/', 'RegisterController@userBase')->name('userBase');
        Route::post('/user-base', 'RegisterController@userBaseSave')->name('userBaseSave');
        //Route::get('/{code}/simple-success', 'RegisterController@simpleSuccess')->name('simpleSuccess');
        //Route::get('/{code}/additional', 'RegisterController@additional')->name('additional');
        //Route::post('/{code}/additional', 'RegisterController@additionalSave')->name('additionalSave');
        Route::get('/{code}/industry', 'RegisterController@industry')->name('industry');
        Route::post('/{code}/industry', 'RegisterController@industrySave')->name('industrySave');
        Route::post('/{code}/phone', 'RegisterController@phone')->name('phoneSave');
        Route::get('/{code}/identity', 'RegisterController@identity')->name('identity');
        Route::post('/{code}/identity', 'RegisterController@identitySave')->name('identitySave');
        Route::post('/{code}/upload-driver', 'RegisterController@uploadDriverLicense')->name('uploadDriver');
        Route::get('/{code}/license', 'RegisterController@license')->name('license');
        Route::post('/{code}/license', 'RegisterController@licenseSave')->name('licenseSave');
        Route::post('/{code}/one-license', 'RegisterController@oneLicenseSave')->name('oneLicenseSave');
        Route::delete('/{code}/licence', 'RegisterController@removeLicense')->name('removeLicense');
        Route::post('/{code}/upload-medical', 'RegisterController@uploadMedicalLicense')->name('uploadMedical');
        Route::post('/{code}/identity-edit', 'RegisterController@identityEdit')->name('identityEdit');
        Route::get('/{code}/check', 'RegisterController@check')->name('check');
        Route::post('/{code}/check', 'RegisterController@checkSave')->name('checkSave');
        Route::get('/{code}/disclosure', 'RegisterController@disclosure')->name('disclosure');
        Route::post('/{code}/disclosure', 'RegisterController@disclosureAccept')->name('disclosure');
        Route::get('/{code}/authorization', 'RegisterController@authorization')->name('authorization');
        Route::post('/{code}/authorization', 'RegisterController@authorizationAccept')->name('authorization');
        Route::get('/{code}/state-disclosure', 'RegisterController@stateDisclosure')->name('stateDisclosure');
        Route::post('/{code}/state-disclosure', 'RegisterController@stateDisclosureAccept')->name('stateDisclosure');
        Route::get('/success', 'RegisterController@success')->name('success');
        Route::get('/autocomplete/{query?}/{lat?}/{lng?}', 'RegisterController@autocomplete')->name('autocomplete');
        Route::get('/place-data/{placeId?}', 'RegisterController@placeData')->name('placeData');
    }
);

Route::group([
    'prefix' => 'base/sign-up',
    'as' => 'base.signup.',
    'namespace' => 'Auth\Partner',
],
    function () {
        Route::post('/user-base', 'RegisterController@userBaseSave')->name('userBaseSave');
        /*Route::get('/{code}/simple-success', 'RegisterController@simpleSuccess')->name('simpleSuccess');
        Route::get('/{code}/additional', 'RegisterController@additional')->name('additional');
        Route::post('/{code}/additional', 'RegisterController@additionalSave')->name('additionalSave');*/
        Route::get('/{code}/details', 'RegisterController@details')->name('details');
        Route::post('/{code}/details', 'RegisterController@userDetailsSave')->name('userDetailsSave');
        Route::get('/success', 'RegisterController@success')->name('success');
    }
);

Route::group([
    'prefix' => 'practice/sign-up',
    'as' => 'practice.signup.',
    'namespace' => 'Auth\Practice',
],
    function () {
        //Route::get('/', 'RegisterController@userBase')->name('userBase');
        Route::post('/user-base', 'RegisterController@userBaseSave')->name('userBaseSave');
        //Route::get('/{code}/simple-success', 'RegisterController@simpleSuccess')->name('simpleSuccess');
        //Route::get('/{code}/additional', 'RegisterController@additional')->name('additional');
        //Route::post('/{code}/additional', 'RegisterController@additionalSave')->name('additionalSave');
        Route::get('/{code}/industry', 'RegisterController@industry')->name('industry');
        Route::post('/{code}/industry', 'RegisterController@industrySave')->name('industrySave');
        Route::get('/autocomplete/{query?}/{lat?}/{lng?}', 'RegisterController@autocomplete')->name('autocomplete');
        Route::get('/place-data/{placeId?}', 'RegisterController@placeData')->name('placeData');
        Route::get('/{code}/base', 'RegisterController@base')->name('base');
        Route::post('/{code}/base', 'RegisterController@baseSave')->name('baseSave');
        Route::get('/{code}/insurance', 'RegisterController@insurance')->name('insurance');
        Route::post('/{code}/insurance', 'RegisterController@insuranceSave')->name('insuranceSave');
        Route::post('/{code}/upload-insurance', 'RegisterController@uploadInsurance')->name('uploadInsurance');
        Route::delete('/{code}/insurance', 'RegisterController@removeInsurance')->name('removeInsurance');
        Route::get('/success', 'RegisterController@success')->name('success');
    }
);
Route::get('/sign-up/{industry?}', 'Auth\RegisterController@userBase')->name('signup.userBase');
Route::get('/sign-up-direct/{type?}', 'Auth\RegisterController@userBaseDirect')->name('signup.userBaseDirect');
Route::get('/r/{code?}', 'Auth\RegisterController@userBaseByInvite')->middleware(\App\Http\Middleware\CheckReferralCode::class)->name('signup.userBaseByInvite');
Route::post('/signup-autosave', 'Auth\RegisterController@autoSave')->name('signup.autoSave');

Route::group([
    'prefix' => 'integration',
    'as' => 'integration.',
    'namespace' => 'Integration'
],
    function () {
        Route::post('driver/dl/{uuid}/update', 'Driver\DriverLicense\DriverLicenseController@updateLicense')
            ->name('driver.driverLicense.updateLicense');

        Route::post('driver/checkr/{uuid}/update', 'Driver\Checkr\CheckrController@update')
            ->name('driver.checkr.update');
    }
);

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'namespace' => 'Admin',
    'middleware' => ['auth', 'can:admin-panel'],
],
    function () {
        Route::group([
            'middleware' => ['can:manage-users'],
        ],
            function () {
                Route::get('autosaves', 'Users\AutoSaveController@index')->name('users.autosaves');
                Route::delete('autosaves/{potUser}', 'Users\AutoSaveController@delete')->name('users.autosaves.delete');
                Route::get('practice/{practice}', 'Users\UsersController@practice')->name('users.practice');
                Route::get('users/approval-list', 'Users\UsersController@approvalList')->name('users.approvalList');
                //Route::resource('users', 'Users\UsersController');
            }
        );

        Route::group([
            'middleware' => ['can:view-users'],
        ],
            function () {
                Route::resource('users', 'Users\UsersController');
                Route::group([
                    'prefix' => 'newsletter',
                    'as' => 'newsletter.',
                    'namespace' => 'NewsLetter',
                ],
                    function () {
                        Route::resource('template', 'TemplateController')->except('show');
                        Route::resource('newsletter', 'NewsLetterController')->except('show');
                    }
                );
            }
        );


        Route::group([
            'middleware' => ['can:manage-machine-learning'],
        ],
            function () {
                Route::get('ml', 'ML\TrainModelItemController@index')->name('ml');
                Route::post('ml', 'ML\TrainModelItemController@store')->name('ml');
            }
        );

        Route::group([
            'prefix' => 'users',
            'as' => 'users.',
            'namespace' => 'Users',
            'middleware' => ['can:manage-users'],
        ],
            function () {
                Route::get('export/users', 'UsersController@exportUsers')->name('exportUsers');
                Route::get('export/autosaves', 'UsersController@exportAutosaves')->name('exportAutosaves');

                Route::get('edit/{user}/user-data', 'EditController@userData')->name('edit.userData');
                Route::post('edit/{user}/user-data', 'EditController@userDataEdit')->name('edit.userData');
                Route::get('edit/invite/{invite}', 'EditController@editInvite')->name('edit.invite');
                Route::post('edit/invite/{invite}', 'EditController@updateInvite')->name('edit.invite');

                Route::group([
                    'prefix' => 'edit/{user}',
                    'as' => 'edit.',
                    'namespace' => 'Provider'
                ],
                    function () {
                        Route::get('position', 'EditController@position')->name('position');
                        Route::post('position', 'EditController@positionEdit')->name('position');
                        Route::post('upload-driver', 'EditController@uploadDriverLicense')->name('uploadDriver');
                        Route::post('phone', 'EditController@phone')->name('phoneSave');
                        Route::get('licenses', 'EditController@licenses')->name('licenses');
                        Route::post('upload-medical', 'EditController@uploadMedicalLicense')->name('uploadMedical');
                        Route::delete('licence', 'EditController@removeLicense')->name('removeLicense');
                        Route::post('licenses', 'EditController@licensesEdit')->name('licenses');
                        Route::post('one-license', 'EditController@oneLicenseSave')->name('oneLicense');
                        Route::get('check', 'EditController@check')->name('check');
                        Route::post('check', 'EditController@checkEdit')->name('check');
                        Route::get('details', 'EditDetailsController@showForm')->name('details');
                        Route::post('details', 'EditDetailsController@store')->name('details');
                        Route::post('avatar', 'EditDetailsController@savePhoto')->name('avatar');
                        Route::post('change-availability', 'EditDetailsController@changeAvailability')->name('changeAvailability');
                        Route::get('rate', 'EditController@rate')->name('rate');
                        Route::post('rate', 'EditController@rateSave')->name('rate');
                    }
                );

                Route::group([
                    'prefix' => 'provider/license/{license}',
                    'as' => 'provider.license.',
                    'namespace' => 'Provider'
                ],
                    function () {
                        Route::post('approve', 'LicenseController@approve')->name('approve');
                        Route::post('decline', 'LicenseController@decline')->name('decline');
                        Route::post('set-base', 'LicenseController@setBaseStatus')->name('setBase');
                    }
                );

                Route::group([
                    'prefix' => 'edit/{user}',
                    'as' => 'edit.',
                    'namespace' => 'Practice'
                ],
                    function () {
                        Route::post('add-rate', 'EditController@addRate')->name('addRate');
                        Route::get('base', 'EditController@base')->name('base');
                        Route::post('base', 'EditController@baseSave')->name('baseSave');
                        Route::get('insurance', 'EditController@insurance')->name('insurance');
                        Route::post('insurance', 'EditController@insuranceSave')->name('insuranceSave');
                        Route::post('upload-insurance', 'EditController@uploadInsurance')->name('uploadInsurance');
                        Route::delete('insurance', 'EditController@removeInsurance')->name('removeInsurance');

                        Route::get('/details-base', 'EditDetailsController@baseDetails')->name('details.base');
                        Route::post('/save-photo', 'EditDetailsController@savePhoto')->name('details.savePhoto');
                        Route::post('/details-base', 'EditDetailsController@saveBaseDetails')->name('details.base');
                        Route::get('/secondary', 'EditDetailsController@secondaryDetails')->name('details.secondary');
                        Route::post('/secondary', 'EditDetailsController@saveSecondaryDetails')->name('details.secondary');
                        Route::get('/team', 'EditDetailsController@team')->name('details.team');
                        Route::post('/team', 'EditDetailsController@teamSave')->name('details.team');
                        Route::delete('/team', 'EditDetailsController@deleteMember')->name('details.team');
                    }
                );

                Route::get('indext/{test?}', 'UsersController@index')->name('indext');
                Route::get('indexr/{rejected}', 'UsersController@index')->name('indexr');
                Route::get('index-deactivated/{deactivated}', 'UsersController@index')->name('index-deactivated');
                //Route::get('approval-list', 'UsersController@approvalList')->name('approvalList');
                Route::post('{user}/check-approve', 'UsersController@checkApprove')->name('check-approve');
                Route::post('{user}/approve', 'UsersController@approve')->name('approve');
                Route::post('{user}/reject', 'UsersController@reject')->name('reject');
                Route::post('{user}/un-reject', 'UsersController@unReject')->name('un-reject');
                Route::post('{user}/approve-provider', 'UsersController@approveProvider')->name('approve-provider');
                Route::post('{user}/approve-practice', 'UsersController@approvePractice')->name('approve-practice');
                Route::post('{user}/compare', 'UsersController@compare')->name('compare');
                Route::post('{user}/set-to-test', 'UsersController@setUserToTest')->name('setToTest');
                Route::get('{user}/email-log', 'UsersController@showEmails')->name('showEmails');
                Route::get('{user}/approval-log', 'UsersController@showApproves')->name('showApproves');
                Route::post('/{user}/login-as', 'UsersController@loginAs')->name('login-as');
                Route::post('resend-message', 'UsersController@resendMessage')->name('resendMessage');
                Route::get('{user}/set-inviter', 'UsersController@inviter')->name('setInviter');
                Route::post('/{user}/set-inviter', 'UsersController@setInviter')->name('setInviter');
                Route::post('/{user}/reset-password-email', 'UsersController@resetPasswordEmail')->name('reset-password-email');
                Route::get('/{user}/login-log', 'UsersController@showLogins')->name('show-logins');
            }
        );
        //Route::get('/', 'HomeController@index')->name('index');

        Route::group([
            'middleware' => ['can:view-shifts'],
        ],
            function () {
                Route::get('shifts/archived', 'Shift\ShiftController@archived')->name('shifts.archived');
                Route::get('shifts/{shift}/log', 'Shift\ShiftController@shiftLog')->name('shifts.log');
                Route::resource('shifts', 'Shift\ShiftController');

                Route::group([
                    'middleware' => ['can:manage-shifts'],
                ],
                    function () {
                        Route::post('shifts/{shift}/refund', 'Shift\ShiftController@refund')->name('shifts.refund');
                        Route::post('shifts/{shift}/cancel', 'Shift\ShiftController@cancel')->name('shifts.cancel');
                        Route::post('shifts/{shift}/archive', 'Shift\ShiftController@archive')->name('shifts.archive');
                        Route::get('shifts/{shift}/invite', 'Shift\ShiftController@inviteProviderForm')->name('shifts.invite');
                        Route::post('shifts/{shift}/check-invite', 'Shift\ShiftController@inviteCheck')->name('shifts.inviteCheck');
                        Route::post('shifts/{shift}/invite', 'Shift\ShiftController@inviteProvider')->name('shifts.invite');
                        Route::post('shifts/{shift}/restart-matching', 'Shift\ShiftController@restartMatching')->name('shifts.restartMatching');


                        Route::resource('coupons', 'Shift\CouponController');
                        Route::get('coupons-auto', 'Shift\CouponController@indexAuto')->name('coupons.auto');

                        Route::group([
                            'prefix' => 'shifts/edit',
                            'as' => 'shifts.edit.',
                            'namespace' => 'Shift'
                        ],
                            function () {
                                Route::get('/time/{shift}', 'ShiftEditController@time')->name('time');
                                Route::post('/time/{shift}', 'ShiftEditController@setTime')->name('time');
                                Route::get('/provider-charge/{charge}',
                                    'ShiftEditController@editProviderCharge')->name('editProviderCharge');
                                Route::post('bonus/{shift}', 'ShiftEditController@changeBonus')->name('bonus');
                                Route::post('/provider-charge/{charge}',
                                    'ShiftEditController@updateProviderCharge')->name('editProviderCharge');
                                Route::post('/provider-charge/{charge}/pay',
                                    'ShiftEditController@payProviderCharge')->name('payProviderCharge');
                            }
                        );
                    }
                );
            }
        );

        Route::group([
            'prefix' => 'data',
            'as' => 'data.',
            'namespace' => 'Data',
            'middleware' => ['can:manage-data'],
        ],
            function () {
                Route::resource('industries', 'IndustryController');
                Route::resource('positions', 'PositionController');
                Route::resource('rates', 'RateController');
                Route::resource('specialities', 'SpecialityController');
                Route::resource('tasks', 'TaskController');
                Route::resource('tools', 'ToolController');
                Route::resource('license_types', 'LicenseTypesController');
                Route::resource('scores', 'ScoreBubblesController');

                Route::get('terms', 'TermsController@index')->name('terms.index');
                Route::get('terms/{term}', 'TermsController@show')->name('terms.show');
                Route::get('create/{term?}', 'TermsController@create')->name('terms.create');
                Route::post('create', 'TermsController@store')->name('terms.store');

                Route::get('privacy', 'PrivacyController@index')->name('privacy.index');
                Route::get('privacy/create/{privacy?}', 'PrivacyController@create')->name('privacy.create');
                Route::post('privacy/create', 'PrivacyController@store')->name('privacy.store');
                Route::get('privacy/{privacy}', 'PrivacyController@show')->name('privacy.show');

                Route::group([
                    'prefix' => 'location',
                    'as' => 'location.',
                    'namespace' => 'Location'
                ],
                    function () {
                        Route::resource('region', 'RegionController');
                        Route::resource('state', 'StateController')->only(['index', 'show']);
                        Route::get('state/{state}/county/{county}', 'CountyController@show')->name('county.show');
                        Route::get('state/{state}/county/{county}/edit', 'CountyController@edit')->name('county.edit');
                        Route::put('state/{state}/county/{county}/update', 'CountyController@update')->name('county.update');
                        Route::get('state/{state}/city/{city}', 'CityController@show')->name('city.show');
                        Route::get('state/{state}/city/{city}/edit', 'CityController@edit')->name('city.edit');
                        Route::put('state/{state}/city/{city}/update', 'CityController@update')->name('city.update');
                        Route::get('state/{state}/areas', 'AreaController@index')->name('area.index');
                        Route::get('state/{state}/area/create', 'AreaController@create')->name('area.create');
                        Route::put('state/{state}/area/create', 'AreaController@store')->name('area.store');
                        Route::get('state/{state}/area/{area}/edit', 'AreaController@edit')->name('area.edit');
                        Route::put('state/{state}/area/{area}/edit', 'AreaController@update')->name('area.update');
                        Route::delete('state/{state}/area/{area}/delete', 'AreaController@destroy')->name('area.destroy');
                    }
                );
            }
        );

        Route::group([
            'prefix' => 'analytics',
            'as' => 'analytics.',
            'namespace' => 'Analytics',
        ],
            function () {

                Route::group([
                    'prefix' => 'transactions',
                    'as' => 'transactions.',
                    'middleware' => ['can:view-transactions'],
                ],
                    function () {
                        Route::get('/practices', 'TransactionController@practices')->name('practices');
                        Route::post('/practices-export', 'TransactionController@exportPracticesCharges')->name('practicesExport');
                        Route::get('/providers', 'TransactionController@providers')->name('providers');
                        Route::post('/providers-export', 'TransactionController@exportProvidersCharges')->name('providersExport');
                        Route::get('/provider-bonuses', 'TransactionController@providerBonuses')->name('providerBonuses');
                        Route::get('/referral-bonuses', 'TransactionController@referralBonuses')->name('invites');
                    }
                );

                Route::group([
                    'middleware' => ['can:admin-analytics'],
                ],
                    function () {
                        Route::get('/', 'AnalyticsController@index')->name('index');
                        Route::get('get-total-number', 'AnalyticsController@getTotalNumber')->name('total-number');
                        Route::get('rejected-to-approved', 'AnalyticsController@findRejectedToApprovedRatio')->name('findRejectedToApprovedRatio');
                        Route::get('approval-time', 'AnalyticsController@approvalTime')->name('approvalTime');
                        Route::get('approval-time-details', 'AnalyticsController@approvalTimeDetails')->name('approvalTimeDetails');
                        Route::get('complete-time-details', 'AnalyticsController@completeTimeDetails')->name('completeTimeDetails');
                        Route::get('complete-time', 'AnalyticsController@completeTime')->name('completeTime');
                        Route::get('get-top-list', 'AnalyticsController@getTopList')->name('top-list');
                        Route::get('get-rated-list/{top}', 'AnalyticsController@getRatedTopList')->name('getRatedTopList');
                        Route::get('map-signups-areas', 'MapController@signupsByAreas')->name('map.signups-areas');
                        Route::get('map-signups', 'MapController@signups')->name('map.signups');
                        Route::get('available/{state?}', 'MapController@available')->name('available');
                        Route::get('profit', 'AnalyticsController@profit')->name('profit');
                        Route::get('profit-by-month', 'AnalyticsController@profitByMonth')->name('profit-by-month');
                        Route::get('future-by-month', 'AnalyticsController@futureByMonth')->name('future-by-month');
                        Route::get('providers', 'AnalyticsController@providers')->name('providers');
                        Route::get('revenue', 'AnalyticsController@revenue')->name('revenue');
                        Route::get('worked', 'AnalyticsController@totalWorked')->name('totalWorked');
                        Route::get('worked-per-day', 'AnalyticsController@totalWorkedPerDay')->name('totalWorkedPerDay');
                        Route::get('avg-matching-time', 'AnalyticsController@avgMatchingTimeDay')->name('avgMatchingTimeDay');
                        Route::get('success-percent', 'AnalyticsController@successPercent')->name('successPercent');
                        Route::get('cancellation-reasons', 'AnalyticsController@cancellationReasons')->name('cancellationReasons');

                        Route::group([
                            'prefix' => 'emails',
                            'as' => 'emails.'
                        ],
                            function () {
                                Route::get('/emails/index', 'EmailsController@index')->name('index');
                                Route::get('/emails/{key}/show', 'EmailsController@show')->name('show');
                            }
                        );

                        Route::get('queue-logs', function () {
                            return view('admin.analytics.logs.horizon');
                        })->name('queue-logs');

                        Route::get('logs', 'LogController@index')->name('logs.index');
                        Route::get('logs/view/{file}', 'LogController@view')->name('logs.view');
                        Route::delete('logs/delete/{file}', 'LogController@delete')->name('logs.delete');

                        Route::get('login-log', 'LoginLogController@index')->name('login-log');

                    }
                );


            }
        );
        Route::group([
            'prefix' => 'mailing',
            'as' => 'mailing.'
        ],
            function () {
                Route::get('/', 'MailingController@index')->name('index');
                Route::post('/referral-info/{test?}', 'MailingController@referralInfo')->name('referralInfo');
            }
        );
    }
);
