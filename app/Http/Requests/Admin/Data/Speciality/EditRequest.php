<?php

namespace App\Http\Requests\Admin\Data\Speciality;

use App\Http\Requests\FormRequest;

/**
 * Class EditRequest
 * @package App\Http\Requests\Admin\Data\Speciality
 */
class EditRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:specialities,title,' . $this->speciality->id,
            'industry' => 'required|integer|exists:industries,id'
        ];
    }
}
