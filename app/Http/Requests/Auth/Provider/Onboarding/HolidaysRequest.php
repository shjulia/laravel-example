<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider\Onboarding;

use App\Http\Requests\FormRequest;

/**
 * Class HolidaysRequest
 * @package App\Http\Requests\Auth\Provider\Onboarding
 */
class HolidaysRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'show-holidays' => 'nullable|boolean',
            'holiday' => 'required_with:show-holidays|nullable|array',
            'holiday.*' => 'boolean'
        ];
    }
}
