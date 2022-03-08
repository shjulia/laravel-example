<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User\Edit;

use App\Http\Requests\FormRequest;

/**
 * Class PositionRequest
 * @package App\Http\Requests\Admin\User\Edit
 */
class PositionRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'position' => 'required|integer|exists:positions,id'
        ];
    }
}
