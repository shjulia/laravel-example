<?php

namespace App\Http\Requests\Auth\Provider;

use App\Entities\User\User;
use App\Http\Requests\FormRequest;

/**
 * Class Step5Request
 * @package App\Http\Requests\Auth\Provider
 */
class CheckRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'ssn' => 'required|string:255',
            'ssn_confirm' => 'required|string:255|same:ssn',
        ];
    }

    public function after($validator)
    {
        $user = User::where('tmp_token', $this->code)->first();
        $provider = $user->specialist;
        $errors = "";
        if (!$provider->driver_zip) {
            $errors .= "zip, ";
        }
        if (!$provider->dob) {
            $errors .= " date of birth, ";
        }
        if (!$user->phone) {
            $errors .= " phone, ";
        }
        if ($errors) {
            $errors = substr($errors, 0, -1);
            $errors .= " must be set.";
            $validator->errors()->add('custom_errors', $errors);
        }
    }
}

/**
 * @SWG\Definition(
 *     definition="CheckRequest",
 *     type="object",
 *     @SWG\Property(
 *          property="ssn",
 *          type="string",
 *          required=true
 *     )
 * )
 */
