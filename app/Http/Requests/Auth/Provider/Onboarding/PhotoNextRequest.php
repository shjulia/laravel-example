<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider\Onboarding;

use App\Http\Requests\FormRequest;

/**
 * Class PhotoNextRequest
 * @package App\Http\Requests\Auth\Provider\Onboarding
 */
class PhotoNextRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
        ];
    }

    /**
     * @param $validator
     */
    public function after($validator)
    {
        if (!($this->user()->specialist->photo ?? false)) {
            $validator->errors()->add('photo', 'Profile photo must be set');
        }
    }
}
