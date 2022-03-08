<?php

declare(strict_types=1);

namespace App\Http\Requests\Shift;

use App\Http\Requests\FormRequest;

/**
 * Class LocationRequest
 * @package App\Http\Requests\Shift
 */
class LocationRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'location' => 'nullable|exists:practice_addresses,id'
        ];
    }
}
