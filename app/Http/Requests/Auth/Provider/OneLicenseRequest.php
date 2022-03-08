<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider;

use App\Http\Requests\FormRequest;

/**
 * Class OneLicenseRequest
 * @package App\Http\Requests\Auth\Provider
 */
class OneLicenseRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => 'nullable|string:50',
            'state' => 'nullable|string:3|exists:states,short_title',
            'number' => 'nullable|string:255',
            'expiration_date' => 'nullable|date',
            'position' => 'integer'
        ];
    }
}
