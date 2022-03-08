<?php

namespace App\Http\Requests\Auth\Practice;

use App\Http\Requests\FormRequest;

/**
 * Class IndustryRequest
 * @package App\Http\Requests\Auth\Practice
 */
class IndustryRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'industry' => 'required|integer|exists:industries,id'
        ];
    }
}

/**
 * @SWG\Definition(
 *     definition="PracticeIndustryRequest",
 *     type="object",
 *     @SWG\Property(property="industry", type="integer", required=true)
 * )
 */
