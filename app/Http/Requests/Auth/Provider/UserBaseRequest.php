<?php

namespace App\Http\Requests\Auth\Provider;

use App\Entities\User\Role;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Step1Request
 * @package App\Http\Requests\Auth\Provider
 */
class UserBaseRequest extends FormRequest
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
            'first_name' => 'required|string:50',
            'last_name' => 'required|string:50',
            'email' => 'required|string|email|max:50|unique_in_the_same_role:' . Role::ROLE_PROVIDER,
        ];
    }
}

/**
 * @SWG\Definition(
 *     definition="UserBaseRequestProvider",
 *     type="object",
 *     @SWG\Property(property="first_name", type="string", required=true),
 *     @SWG\Property(property="last_name", type="string", required=true),
 *     @SWG\Property(property="email", type="string", required=true),
 *     @SWG\Property(property="industry", type="integer")
 * )
 */
