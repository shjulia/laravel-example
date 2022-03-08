<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider\Onboarding;

use App\Http\Requests\FormRequest;

/**
 * Class SpecialitiesRequest
 * @package App\Http\Requests\Auth\Provider\Onboarding
 */
class SpecialitiesRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'specialities' => 'required|array',
            'specialities.*' => 'integer|exists:specialities,id',
        ];
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function getValidatorInstance()
    {
        $this->merge([
            'specialities' => $this->specialities ? explode(',', $this->specialities) : null,
        ]);
        return parent::getValidatorInstance();
    }
}
