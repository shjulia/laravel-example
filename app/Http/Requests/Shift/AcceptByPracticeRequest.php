<?php

namespace App\Http\Requests\Shift;

use App\Http\Requests\FormRequest;

/**
 * Class AcceptByPracticeRequest
 * @package App\Http\Requests\Shift
 */
class AcceptByPracticeRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'arrival' => 'required|numeric'
        ];
    }
}
