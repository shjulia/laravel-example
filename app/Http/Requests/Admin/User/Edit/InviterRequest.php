<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User\Edit;

use App\Http\Requests\FormRequest;

/**
 * Class InviterRequest
 * @package App\Http\Requests\Admin\User\Edit
 */
class InviterRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:referrals,user_id',
            'pay' => 'nullable|boolean'
        ];
    }
}
