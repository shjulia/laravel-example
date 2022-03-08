<?php

namespace App\Http\Requests\Admin\Data\Industry;

use App\Http\Requests\FormRequest;

/**
 * Class EditRequest
 * @package App\Http\Requests\Admin\Data\Industry
 */
class EditRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:industries,title,' . $this->industry->id,
            'alias' => 'required|string|max:255|unique:industries,alias,' . $this->industry->id,
        ];
    }
}
