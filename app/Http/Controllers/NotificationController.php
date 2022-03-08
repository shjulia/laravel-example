<?php

namespace App\Http\Controllers;

use App\Entities\Notification\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class NotificationController
 * @package App\Http\Controllers
 */
class NotificationController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request)
    {
        $request->validate(['id' => 'required|integer']);
        try {
            Notification::where('id', $request->id)->update(['read_at' => Carbon::now()]);
        } catch (\Exception $e) {
            \LogHelper::error($e);
            return \Response::json('Something went wrong', 500);
        }

        return \Response::json('OK', 200);
    }
}
