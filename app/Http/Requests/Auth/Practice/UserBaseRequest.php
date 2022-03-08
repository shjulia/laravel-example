<?php

namespace App\Http\Requests\Auth\Practice;

use App\Entities\User\Role;
use App\Http\Requests\FormRequest;

/**
 * Class UserBaseRequest
 * @package App\Http\Requests\Auth\Practice
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
            'email' => 'required|string|email|max:50|unique_in_the_same_role:' . Role::ROLE_PRACTICE,
        ];
    }
}

/**
 * @SWG\Definition(
 *     definition="UserBaseRequest",
 *     type="object",
 *     @SWG\Property(property="first_name", type="string", required=true),
 *     @SWG\Property(property="last_name", type="string", required=true),
 *     @SWG\Property(property="email", type="string", required=true),
 *     @SWG\Property(property="industry", type="integer")
 * )
 */
