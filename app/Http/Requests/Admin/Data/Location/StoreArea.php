<?php

namespace App\Http\Requests\Admin\Data\Location;

use App\Http\Requests\FormRequest;

/**
 * Class StoreArea
 * @package App\Http\Requests\Admin\Data\Location
 */
class StoreArea extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'tier' => 'required|numeric',
            'cities' => 'required_without:zip_codes|array',
            'cities.*' => 'integer',
            'zip_codes' => 'required_without:cities|array',
            'zip_codes.*' => 'integer',
            'is_open' => 'integer'
        ];
    }
}
