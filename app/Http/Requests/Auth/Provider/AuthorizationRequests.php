<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider;

use App\Http\Requests\FormRequest;

/**
 * Class AuthorizationRequests
 * @package App\Http\Requests\Auth\Provider
 */
class AuthorizationRequests extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'full_name' => 'required|string:255',
            'g-recaptcha-response' => 'required|recaptcha',
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'g-recaptcha-response.required' => 'Captcha is required.',
            'g-recaptcha-response.recaptcha' => 'Captcha is not valid'
        ];
    }
}
