<?php

namespace App\Http\Requests\Referral;

use App\Http\Requests\FormRequest;

/**
 * Class InviteRequest
 * @package App\Http\Requests\Referral
 */
class InviteRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email|unique:invites,email'
        ];
    }
}
