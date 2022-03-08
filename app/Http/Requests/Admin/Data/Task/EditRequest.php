<?php

namespace App\Http\Requests\Admin\Data\Task;

use App\Http\Requests\FormRequest;

/**
 * Class EditRequest
 * @package App\Http\Requests\Admin\Data\Task
 */
class EditRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:tasks,title,' . $this->task->id,
            'positions' => 'integer|exists:positions,id'
        ];
    }
}
