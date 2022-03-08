<?php

namespace App\Http\Requests\Auth\Practice;

use App\Http\Requests\FormRequest;

/**
 * Class BaseInfoRequest
 * @package App\Http\Requests\Auth\Practice
 */
class BaseInfoRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'address' => 'required|string:255',
            'city' => 'required|string:255',
            'state' => 'required|string:20',
            'zip' => 'required|string:10',
            'name' => 'required|string:255',
            'url' => 'nullable|string:255',
            'phone' => 'required|size:14'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'phone.size' => 'The phone must be 10 characters.'
        ];
    }
}

/**
 * @SWG\Definition(
 *     definition="PracticeBaseInfoRequest",
 *     type="object",
 *     @SWG\Property(property="address", type="string", required=true),
 *     @SWG\Property(property="city", type="string", required=true),
 *     @SWG\Property(property="state", type="string", required=true, description="Use short title"),
 *     @SWG\Property(property="zip", type="string", required=true),
 *     @SWG\Property(property="name", type="string", required=true),
 *     @SWG\Property(property="url", type="string"),
 *     @SWG\Property(property="phone", type="string")
 * )
 */
