<?php

namespace App\Http\Validation;

use App\Entities\User\User;
use Illuminate\Validation\Validator;

/**
 * Class CustomValidation
 * @package App\Http\Validation
 */
class CustomValidation extends Validator
{
    public function validateUniqueInTheSameRole($attribute, $value, $parameters)
    {
        $role = $parameters[0];
        $user = User::where('email', $value)
            ->whereHas('roles', function ($query) use ($role) {
                $query->where('type', $role);
            })
            ->first();
        if ($user) {
            $this->errors()->add($attribute, 'The email has already been taken.');
        }
        return true;
    }

    public function validatePassword($attribute, $value, $parameters, $validator)
    {
        if (
            preg_match(
                '/^\S*(?=\S*[a-zA-Z])(?=\S*[\d])(?=\S*[\*\-\+\!\@\#\$\%\^\&\(\)\=\_\.\,\;\:\~\`])\S*$/',
                $value
            )
        ) {
            return true;
        }
        $this->errors()->add($attribute, 'Password must contain at least one number or symbol.');
        return false;
    }

    public function validateBase64($attribute, $value, $parameters, $validator)
    {
        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $value)) {
            return true;
        }
        $this->errors()->add($attribute, 'Field value is invalid');
        return false;
    }

    public function validateBase64image($attribute, $value, $parameters, $validator)
    {
        if (!$value || $value == "false") {
            return true;
        }
        $explode = explode(',', $value);
        $allow = ['png', 'jpg', 'svg', 'gif', 'jpeg'];
        $format = str_replace(
            [
                'data:image/',
                ';',
                'base64',
            ],
            [
                '', '', '',
            ],
            $explode[0]
        );
        // check file format
        if (!in_array($format, $allow)) {
            $this->errors()->add($attribute, 'The ' . $attribute . ' has not allowed format');
            return false;
        }
        // check base64 format
        if (!preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $explode[1])) {
            $this->errors()->add($attribute, 'Field value is invalid');
            return false;
        }
        return true;
    }
}
