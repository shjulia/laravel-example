<?php

namespace App\Http\Requests\Auth\Practice;

use App\Http\Requests\FormRequest;

/**
 * Class InsuranceRequest
 * @package App\Http\Requests\Auth\Practice
 */
class InsuranceRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => 'nullable|required_without:no_policy|string:50',
            'number' => 'nullable|required_without:no_policy|string:255',
            'expiration_date' => 'nullable|required_without:no_policy|date',
            'policy_provider' => 'nullable|string:255',
            'no_policy' => 'nullable|required_without:number,type,expiration_date|boolean'
        ];
    }
}

/**
 * @SWG\Definition(
 *     definition="PracticeInsuranceRequest",
 *     type="object",
 *     @SWG\Property(property="type", type="string", required=true),
 *     @SWG\Property(property="number", type="string", required=true),
 *     @SWG\Property(property="expiration_date", type="date", required=true),
 *     @SWG\Property(property="policy_provider", type="string", required=true),
 *     @SWG\Property(property="no_policy", type="boolean"),
 * )
 */
