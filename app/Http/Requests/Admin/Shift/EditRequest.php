<?php

namespace App\Http\Requests\Admin\Shift;

use App\Http\Requests\FormRequest;

/**
 * Class EditRequest
 * @package App\Http\Requests\Admin\Shift
 */
class EditRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'provider_id' => 'required|integer|exists:specialists,user_id'
        ];
    }
}
