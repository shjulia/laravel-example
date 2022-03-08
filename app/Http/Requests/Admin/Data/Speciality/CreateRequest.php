<?php

namespace App\Http\Requests\Admin\Data\Speciality;

use App\Http\Requests\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Admin\Data\Speciality
 */
class CreateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:specialities,title',
            'industry' => 'required|integer|exists:industries,id'
        ];
    }
}
