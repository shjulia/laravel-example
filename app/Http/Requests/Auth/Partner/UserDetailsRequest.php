<?php

namespace App\Http\Requests\Auth\Partner;

use App\Http\Requests\FormRequest;

/**
 * Class UserDetailsRequest
 * @package App\Http\Requests\Auth\Partner
 */
class UserDetailsRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'description' => 'required|string:50',
            'description_answer' => 'string:255'
        ];
    }
}
