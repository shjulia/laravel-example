<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Shift;

use App\Http\Requests\FormRequest;

/**
 * Class InviteRequest
 * @package App\Http\Requests\Admin\Shift
 */
class InviteRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'provider_id' => 'required|integer|exists:specialists,user_id'
        ];
    }
}
