<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider\Onboarding;

use App\Http\Requests\FormRequest;

/**
 * Class DistanceRequest
 * @package App\Http\Requests\Auth\Provider\Onboarding
 */
class DistanceRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'is_duration' => 'nullable|required_without:is_distance|boolean',
            'is_distance' => 'nullable|required_without:is_duration|boolean',
            'duration' => 'nullable|required_with:is_duration|integer|min:5|max:120',
            'distance' => 'nullable|required_with:is_distance|integer|min:4|max:150',
        ];
    }
}
