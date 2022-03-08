<?php

use Illuminate\Http\Request;
use Laravel\Passport\Passport;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/', 'Api\HomeController@index');

Route::post('/save-push', 'PushController@store');

Route::group([
    'middleware' => \App\Http\Middleware\PassportMiddleware::class
],
    function () {
        Passport::routes();
    }
);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group([
    'namespace' => 'Api'
],
    function () {

        Route::group([
            'prefix' => 'data',
            'namespace' => 'Data'
        ],
            function () {
                Route::get('states', 'DataController@states');
                Route::get('holidays', 'DataController@holidays');
                Route::get('roles', 'DataController@roles');
                Route::get('license-types', 'DataController@licenseTypes');
                Route::get('direct-license-types/{position}/{state}', 'DataController@directLicenseTypes');
                Route::get('practice-roles', 'DataController@practiceRoles');

                Route::get('industries', 'IndustryController@industries');
                Route::get('positions/{industry}', 'IndustryController@positions');
                Route::get('specialities/{industry}', 'IndustryController@specialities');
            }
        );
        Route::group([
            'prefix' => 'practice/sign-up',
            'as' => 'practice.signup.',
            'namespace' => 'Auth\Practice',
        ],
            function () {
                Route::post('/user-base', 'RegisterController@userBaseSave');
                Route::post('/{code}/additional', 'RegisterController@additionalSave');
                Route::get('/autocomplete/{query?}/{lat?}/{lng?}', 'RegisterController@autocomplete');
                Route::get('/place-data/{placeId?}', 'RegisterController@placeData');
                Route::post('/{code}/industry', 'RegisterController@industrySave');
                Route::post('/{code}/base', 'RegisterController@baseSave');
                Route::post('/{code}/insurance', 'RegisterController@insuranceSave');
                Route::post('/{code}/upload-insurance', 'RegisterController@uploadInsurance');
            }
        );

        Route::group([
            'prefix' => 'sign-up',
            'as' => 'signup.',
            'namespace' => 'Auth\Provider',
        ],
            function () {
                Route::post('/user-base', 'RegisterController@userBaseSave');
                Route::post('/{code}/additional', 'RegisterController@additionalSave');
                Route::post('/{code}/industry', 'RegisterController@industrySave');
                Route::post('/{code}/identity', 'RegisterController@identitySave');
                Route::post('/{code}/upload-driver', 'RegisterController@uploadDriverLicense');
                Route::post('/{code}/license', 'RegisterController@licenseSave');
                Route::delete('/{code}/license', 'RegisterController@removeLicense');
                Route::post('/{code}/upload-medical', 'RegisterController@uploadMedicalLicense');
                Route::post('/{code}/check', 'RegisterController@checkSave');
            }
        );

        Route::group([
            'middleware' => ['auth:api']
        ], function () {
            Route::get('/user-data', 'UserController@getUserData');
        });

        Route::group([
            'middleware' => ['auth:api', 'App\Http\Middleware\Auth\FinishSignup']
        ], function () {

            Route::get('/account-detail', 'Auth\Provider\DetailsController@showForm');
            Route::post('/save-photo', 'Auth\Provider\DetailsController@savePhoto');
            Route::post('/save-details', 'Auth\Provider\DetailsController@store');

            Route::group([
                'prefix' => 'practice/details',
                'as' => 'practice.details.',
                'namespace' => 'Auth\Practice',
            ],
                function () {
                    Route::post('/save-photo', 'DetailsController@savePhoto')->name('savePhoto');
                    Route::post('/save-practice-details', 'DetailsController@saveBaseDetails');
                    Route::post('/secondary', 'DetailsController@saveSecondaryDetails');
                    Route::get('/team', 'DetailsController@team');
                    Route::post('/team', 'DetailsController@teamSave');
                    Route::delete('/team', 'DetailsController@deleteMember');
                    Route::post('/save-billing', 'DetailsController@billingSave');
                }
            );
        });
});

