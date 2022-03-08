<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider\Onboarding;

use App\Http\Requests\FormRequest;

/**
 * Class RateRequest
 * @package App\Http\Requests\Auth\Provider\Onboarding
 */
class RateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'rate' => 'required|numeric|max:500|min:1',
        ];
    }
}
