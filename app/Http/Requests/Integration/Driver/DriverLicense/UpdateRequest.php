<?php

declare(strict_types=1);

namespace App\Http\Requests\Integration\Driver\DriverLicense;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => 'required|uuid'
        ];
    }
}
