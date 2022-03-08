<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Shift\Coupon;

use App\Http\Requests\FormRequest;

/**
 * Class EditRequest
 * @package App\Http\Requests\Admin\Shift\Coupon
 */
class EditRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'dollar_off' => 'required_without:percent_off|nullable|numeric|gt:0',
            'percent_off' => 'required_without:dollar_off|nullable|numeric|gt:0',
            'minimum_bill' => 'nullable|numeric',
            'use_per_account_limit' => 'nullable|integer',
            'use_globally_limit' => 'nullable|integer',
            'state.*' => 'nullable|integer|exists:states,id',
            'position.*' => 'nullable|integer|exists:positions,id'
        ];
    }
}
