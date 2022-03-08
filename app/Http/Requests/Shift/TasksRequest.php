<?php

namespace App\Http\Requests\Shift;

use App\Http\Requests\FormRequest;

/**
 * Class TasksRequest
 * @package App\Http\Requests\Shift
 */
class TasksRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'tasks' => 'nullable|array',
            'tasks.*' => 'integer|exists:tasks,id',
            'settasks' => 'boolean'
        ];
    }
}
