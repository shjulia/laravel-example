<?php

namespace App\Http\Requests\Auth\Provider;

use App\Entities\User\User;
use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Class Step3Request
 * @package App\Http\Requests\Auth\Provider
 */
class IdentityRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string:255',
            'last_name' => 'nullable|string:255',
            'middle_name' => 'nullable|string:255',
            'address' => 'required|string:255',
            'city' => 'required|string:255',
            'state' => 'required|string:20',
            'zip' => 'required|string:10',
            'dob' => 'required|date',
            'expiration_date' => 'required|date',
            'gender' => ['required', 'string:2', Rule::in(['M', 'F'])],
            'license' => 'required|string:255'
        ];
    }

    /**
     * @param $validator
     */
    public function after($validator)
    {
        $user = $this->getUserByParams();

        if (!$user->phone && !$this->isUserAdmin()) {
            $validator->errors()->add('phone', 'Phone must be set');
        }
    }

    /**
     * @return User
     */
    private function getUserByParams(): User
    {
        if ($this->user) {
            return User::where('id', $this->user->id)->first();
        }
        if (!$this->code) {
            return Auth::user();
        }
        return User::where('tmp_token', $this->code)->first();
    }

    public function isUserAdmin(): bool
    {
        $authUser = Auth::user();
        return $authUser ? $authUser->isAdminStatuses() : false;
    }
}

/**
 * @SWG\Definition(
 *     definition="IdentityRequest",
 *     type="object",
 *     @SWG\Property(property="address", type="string", required=true),
 *     @SWG\Property(property="city", type="string", required=true),
 *     @SWG\Property(property="state", type="string", required=true),
 *     @SWG\Property(property="zip", type="string", required=true),
 *     @SWG\Property(property="dob", type="string", required=true),
 *     @SWG\Property(property="expiration_date", type="string", required=true),
 *     @SWG\Property(property="license", type="string", required=true),
 *     @SWG\Property(property="gender", type="string", description="M|F"),
 * )
 */
