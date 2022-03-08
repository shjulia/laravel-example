<?php

namespace App\Http\Requests\Auth\Practice\Details;

use App\Http\Requests\FormRequest;

/**
 * Class BaseDetailsRequest
 * @package App\Http\Requests\Auth\Practice\Details
 */
class BaseDetailsRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            //'photo' => 'nullable|image|mimes:jpg,jpeg,png',
            //'photo' => 'base64image',
            'culture' => 'nullable|string',
            'notes' => 'nullable|string',
            'on_site_contact' => 'nullable|string:255'
        ];
    }

    /**
     * @param $validator
     */
    public function after($validator)
    {
        if (!($this->user()->practice->practice_photo ?? false)) {
            $validator->errors()->add('photo', 'Photo must be set');
        }
    }
}


/**
 * @SWG\Definition(
 *     definition="BaseDetailsRequest",
 *     type="object",
 *     @SWG\Property(property="culture", type="string"),
 *     @SWG\Property(property="notes", type="string"),
 *     @SWG\Property(property="on_site_contact", type="string"),
 * )
 */
