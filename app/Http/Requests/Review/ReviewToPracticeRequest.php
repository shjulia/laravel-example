<?php

namespace App\Http\Requests\Review;

use App\Http\Requests\FormRequest;

/**
 * Class ReviewToPracticeRequest
 * @package App\Http\Requests\Review
 */
class ReviewToPracticeRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'text' => 'nullable|string',
            'score' => 'required|numeric|min:1|max:5',
        ];
    }
}
