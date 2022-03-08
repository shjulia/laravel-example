<?php

namespace App\Http\Requests\Auth\Provider;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Step2Request
 * @package App\Http\Requests\Auth\Provider
 */
class IndustryRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'industry' => 'required|integer|exists:industries,id',
            'position' => 'required|integer|exists:positions,id'
        ];
    }
}

/**
 * @SWG\Definition(
 *     definition="ProviderIndustryRequest",
 *     type="object",
 *     @SWG\Property(property="industry", type="integer", required=true),
 *     @SWG\Property(property="position", type="integer", required=true)
 * )
 */
