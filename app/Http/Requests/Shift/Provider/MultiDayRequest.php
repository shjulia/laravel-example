<?php

declare(strict_types=1);

namespace App\Http\Requests\Shift\Provider;

use App\Http\Requests\FormRequest;

/**
 * Class MultiDayRequest
 * @package App\Http\Requests\Shift\Provider
 */
class MultiDayRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'shifts' => 'nullable|array',
            'shifts.*' => 'integer|exists:shifts,id'
        ];
    }
}
