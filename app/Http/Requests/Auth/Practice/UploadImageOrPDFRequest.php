<?php

namespace App\Http\Requests\Auth\Practice;

use App\Http\Requests\FormRequest;

/**
 * Class UploadImageOrPDFRequest
 * @package App\Http\Requests\Auth\Practice
 */
class UploadImageOrPDFRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'photo' => 'required|file|mimes:jpg,jpeg,png,pdf,gif',
        ];
    }
}
