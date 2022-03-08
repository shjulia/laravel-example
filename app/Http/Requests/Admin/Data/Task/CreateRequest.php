<?php

namespace App\Http\Requests\Admin\Data\Task;

use App\Http\Requests\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Admin\Data\Task
 */
class CreateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:positions,title',
            'position ' => 'nullable|exists:positions,id'
        ];
    }
}
