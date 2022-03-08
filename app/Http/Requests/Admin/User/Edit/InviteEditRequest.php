<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\User\Edit;

use App\Entities\Invite\Invite;
use App\Entities\Payment\ProviderCharge;
use App\Http\Requests\FormRequest;

/**
 * Class InviteEditRequest
 * @package App\Http\Requests\Admin\User\Edit
 */
class InviteEditRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'payment_system' => 'required|string|in:' . implode(',', ProviderCharge::paymentSystemLists()),
            'status' => 'required|string|in:' . implode(',', Invite::statusesLists()),
            'charge_id' => 'nullable|string'
        ];
    }
}
