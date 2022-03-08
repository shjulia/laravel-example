<?php

namespace App\Http\Requests\Admin\Data\Review\Score;

use App\Http\Requests\FormRequest;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Admin\Data\Review\Score
 */
class CreateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'for_type' => 'required|string|max:255',
            'active' => 'nullable|boolean'
        ];
    }
}
