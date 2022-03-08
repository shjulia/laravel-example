<?php

namespace App\Providers;

use App\Http\Composers\DaysComposer;
use App\Http\Composers\LayoutComposer;
use App\Http\Composers\StateComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

/**
 * Class ViewServiceProvider
 * @package App\Providers
 */
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(
            [
                'layouts.app',
                'layouts.auth',
                'layouts.main',
                'layouts.onboarding'
            ],
            LayoutComposer::class
        );
        View::composer(
            [
                'register.provider.identity',
                'register.provider.license',
                'register.practice.base',
                'register.provider.check',
                'register.provider.details._forms',
                'register.practice.details.locations',
                'admin.users.edit.provider.identity',
                'license._form',
                'admin.users.edit.practice.base',
                'admin.users.index',
                'account.provider.edit.identity',
                'admin.analytics.available',
                'admin.shift.coupon.create',
                'admin.shift.coupon.edit',
                'admin.analytics.available'
            ],
            StateComposer::class
        );
        View::composer(
            [
                'register.provider.details._forms',
                'admin.users.show._show-provider',
                'admin.analytics.signup-map',
                'register.provider.onboarding.availability'
            ],
            DaysComposer::class
        );
        View::composer(
            [
                'register.provider.authorization'
            ],
            function ($view) {
                $view->with('reCaptchaKey', config('services.re_captcha.public_key'));
            }
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
