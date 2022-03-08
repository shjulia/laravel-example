<?php

namespace App\Http\Requests\Admin\Analytics;

use App\Http\Requests\FormRequest;

/**
 * Class DateRequest
 * @package App\Http\Requests\Admin\Analytics
 */
class DateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'position' => 'nullable|exists:positions,id'
        ];
    }
}
