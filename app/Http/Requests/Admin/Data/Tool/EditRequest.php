<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Data\Tool;

use App\Http\Requests\FormRequest;

/**
 * Class EditRequest
 * @package App\Http\Requests\Admin\Data\Tool
 */
class EditRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:tools,title,' . $this->tool->id,
        ];
    }
}
