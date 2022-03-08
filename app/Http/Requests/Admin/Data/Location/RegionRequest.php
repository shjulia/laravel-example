<?php

namespace App\Http\Requests\Admin\Data\Location;

use App\Http\Requests\FormRequest;

/**
 * Class RegionRequest
 * @package App\Http\Requests\Admin\Data\Location
 */
class RegionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'states' => 'required|array',
            'states.*' => 'integer',

        ];
    }
}
