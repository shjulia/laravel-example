<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth\Practice\Details;

use App\Http\Requests\FormRequest;

/**
 * Class ToolRequest
 * @package App\Http\Requests\Auth\Practice\Details
 */
class ToolRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'tool' => 'nullable|string|max:255|exists:tools,id'
        ];
    }
}
