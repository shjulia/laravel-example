<?php

namespace App\Providers;

use App\Http\Validation\CustomValidation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

/**
 * Class ValidationServiceProvider
 * @package App\Providers
 */
class ValidationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->app->validator->resolver(function ($translator, $data, $rules, $messages) {
            return new CustomValidation($translator, $data, $rules, $messages);
        });
        Validator::extend('recaptcha', 'App\\Http\\Validation\\ReCaptcha@validate');
    }
}
