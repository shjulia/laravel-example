<?php

namespace App\Http\Requests\Admin\Data\LicenseType;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Admin\Data\LicenseType
 */
class CreateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'position.*' => 'required|integer|exists:positions,id',
            'states.*' => 'required|array',
            'required.*' => 'boolean'
        ];
    }
}
