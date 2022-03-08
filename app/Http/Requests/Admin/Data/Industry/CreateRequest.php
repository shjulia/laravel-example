<?php

namespace App\Http\Requests\Admin\Data\Industry;

use App\Http\Requests\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Admin\Data\Industry
 */
class CreateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:industries,title',
            'alias' => 'required|string|max:255|unique:industries,alias',
        ];
    }
}
