<?php

namespace App\Http\Requests\General;

use App\Http\Requests\FormRequest;

/**
 * Class EmailRequest
 * @package App\Http\Requests\General
 */
class EmailRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
          'email' => 'required|email'
        ];
    }
}
