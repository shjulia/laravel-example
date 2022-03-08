<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

/**
 * Class AdditionalRequest
 * @package App\Http\Requests\Auth
 */
class AdditionalRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'token' => 'required|string',
            'phone' => 'nullable|string',
            'password' => 'required|string|min:12|max:50|password'
        ];
    }
}

/**
 * @SWG\Definition(
 *     definition="SignupAdditionalRequest",
 *     type="object",
 *     @SWG\Property(property="password", type="string", required=true),
 *     @SWG\Property(property="phone", type="string")
 * )
 */
