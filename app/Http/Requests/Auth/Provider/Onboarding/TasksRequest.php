<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Provider\Onboarding;

use App\Http\Requests\FormRequest;

/**
 * Class TasksRequest
 * @package App\Http\Requests\Auth\Provider\Onboarding
 */
class TasksRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'tasks' => 'required|array',
            'tasks.*' => 'integer|exists:tasks,id',
        ];
    }

    /**
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function getValidatorInstance()
    {
        $this->merge([
            'tasks' => $this->tasks ? explode(',', $this->tasks) : null,
        ]);
        return parent::getValidatorInstance();
    }
}
