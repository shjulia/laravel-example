<?php

namespace App\Http\Requests\Referral;

use App\Http\Requests\FormRequest;

/**
 * Class CodeRequest
 * @package App\Http\Requests\Referral
 */
class CodeRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $referralId = $this->user()->id;
        return [
            'code' => 'required|string|max:19|unique:referrals,referral_code,' . $referralId . ',user_id'
        ];
    }
}
