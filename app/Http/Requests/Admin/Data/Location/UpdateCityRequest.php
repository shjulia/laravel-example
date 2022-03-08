<?php

namespace App\Http\Requests\Admin\Data\Location;

use App\Http\Requests\FormRequest;

/**
 * Class UpdateCityRequest
 * @package App\Http\Requests\Admin\Data\Location
 */
class UpdateCityRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'tier' => 'required|numeric',
        ];
    }
}
