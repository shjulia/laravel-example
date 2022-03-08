<?php

namespace App\Http\Requests\General;

use App\Http\Requests\FormRequest;

/**
 * Class PhotoRequest
 * @package App\Http\Requests\General
 */
class PhotoRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'file' => 'image|mimes:jpg,jpeg,png',
        ];
    }
}
