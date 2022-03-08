<?php

namespace App\Http\Requests\Admin\Data\Position;

use App\Http\Requests\FormRequest;

/**
 * Class EditRequest
 * @package App\Http\Requests\Admin\Data\Position
 */
class EditRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:positions,title,' . $this->position->id,
            'industry' => 'required|integer|exists:industries,id',
            'parent_id' => 'nullable|integer|exists:positions,id',
            'fee' => 'required|numeric|min:1',
            'minimum_profit' => 'required|numeric|min:1',
            'surge_price' => 'required|numeric|min:1'
        ];
    }
}
