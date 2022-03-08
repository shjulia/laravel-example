<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider\Onboarding;

use App\Http\Requests\FormRequest;

/**
 * Class LengthRequest
 * @package App\Http\Requests\Auth\Provider\Onboarding
 */
class LengthRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'min' => 'required|integer|max:12|min:2',
            'max' => 'required|integer|max:12|min:2'
        ];
    }
}
