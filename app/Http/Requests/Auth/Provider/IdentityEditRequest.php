<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider;

use App\Http\Requests\FormRequest;

/**
 * Class IdentityEditRequest
 * @package App\Http\Requests\Auth\Provider
 */
class IdentityEditRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string:255',
            'last_name' => 'required|string:255',
            'middle_name' => 'nullable|string:255',
            'address' => 'required|string:255',
            'city' => 'required|string:255',
            'state' => 'required|string:20',
            'zip' => 'required|string:10',
            'phone' => 'required|string',
            'dob' => 'required|date'
        ];
    }
}
