<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider\Onboarding;

use App\Http\Requests\FormRequest;

/**
 * Class PaymentRequest
 * @package App\Http\Requests\Auth\Provider\Onboarding
 */
class PaymentRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'is_standard' => 'nullable|required_without:is_expedited|boolean',
            'is_expedited' => 'nullable|required_without:is_standard|boolean',
            'routing_number' => 'nullable|digits_between:9,10',
            'account_number' => 'nullable|digits_between:1,16',
        ];
    }
}
