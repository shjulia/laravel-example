<?php

namespace App\Http\Requests\Auth\Partner;

use App\Http\Requests\FormRequest;

/**
 * Class UserBaseRequest
 * @package App\Http\Requests\Auth\Partner
 */
class UserBaseRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string:50',
            'last_name' => 'required|string:50',
            'email' => 'required|string|email|max:50|unique:users,email',
        ];
    }
}
