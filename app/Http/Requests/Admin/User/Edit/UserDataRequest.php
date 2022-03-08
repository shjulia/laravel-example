<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User\Edit;

use App\Http\Requests\FormRequest;

/**
 * Class UserDataRequest
 * @package App\Http\Requests\Admin\User\Edit
 */
class UserDataRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required|string:50',
            'last_name' => 'required|string:50',
            'email' => 'required|string|email|max:50|unique:users,email,' . $this->user_id,
            'phone' => 'nullable|string',
            'password' => 'nullable|string'
        ];
    }
}
