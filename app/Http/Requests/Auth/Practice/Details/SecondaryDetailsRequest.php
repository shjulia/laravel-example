<?php

namespace App\Http\Requests\Auth\Practice\Details;

use App\Http\Requests\FormRequest;

/**
 * Class SecondaryDetailsRequest
 * @package App\Http\Requests\Auth\Practice\Details
 */
class SecondaryDetailsRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'park' => 'nullable|string',
            'door' => 'nullable|string',
            'dress_code' => 'nullable|string:255',
            'info' => 'nullable|string:255'
        ];
    }
}

/**
 * @SWG\Definition(
 *     definition="SecondaryDetailsRequest",
 *     type="object",
 *     @SWG\Property(property="park", type="string"),
 *     @SWG\Property(property="door", type="string"),
 *     @SWG\Property(property="dress_code", type="string"),
 *     @SWG\Property(property="info", type="string"),
 * )
 */
