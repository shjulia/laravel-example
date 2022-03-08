<?php

namespace App\Http\Requests\Shift;

use App\Http\Requests\FormRequest;

/**
 * Class ShiftBaseRequest
 * @package App\Http\Requests\Shift
 */
class ShiftBaseRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            //'industry' => 'required|integer|exists:industries,id',
            'position' => 'required|integer|exists:positions,id'
        ];
    }
}
