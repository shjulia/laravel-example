<?php

use App\Entities\User\User;

/**
 * @param \Illuminate\Support\Carbon|null $time
 * @return string
 */
function formatedTimestamp(?\Illuminate\Support\Carbon $time): string
{
    if (!$time) {
        return '';
    }
    $tz = Auth::user()->tz ?? 0;

    return $time->tz($tz)->format('g:i:s A F j, Y');
}

function walletClientUrl(User $user): string
{
    return env('CORE_URL', '') . '/admin/wallet/client/' . ($user->wallet->wallet_client_id ?? '') . '/view';
}
