<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider\Onboarding;

use App\Http\Requests\FormRequest;

/**
 * Class AvailabilityRequest
 * @package App\Http\Requests\Auth\Provider\Onboarding
 */
class AvailabilityRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'from.*' => 'required|regex:/^[0-9\-]{2}:[0-9\-]{2}$/i',
            'to.*' => 'required|regex:/^[0-9\-]{2}:[0-9\-]{2}$/i',
        ];
    }
}
