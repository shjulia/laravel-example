<?php

namespace App\Http\Requests\Auth\Practice\Details;

use App\Entities\User\Role;
use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class TeamMemberRequest
 * @package App\Http\Requests\Auth\Practice\Details
 */
class TeamMemberRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string:50',
            'last_name' => 'required|string:50',
            'email' => 'required|string|email|max:50|unique:users,email,' . $this->user_id,
            'role' => ['required', 'string', Rule::in(array_keys(Role::practiceRoles()))],
            'user_id' => 'nullable|integer|exists:users,id'
        ];
    }
}

/**
 * @SWG\Definition(
 *     definition="TeamMemberRequest",
 *     type="object",
 *     @SWG\Property(property="first_name", type="string", required=true),
 *     @SWG\Property(property="last_name", type="string", required=true),
 *     @SWG\Property(property="email", type="string", required=true),
 *     @SWG\Property(property="role", type="string", required=true),
 *     @SWG\Property(property="user_id", type="string", description="If edit user must be set")
 * )
 */
