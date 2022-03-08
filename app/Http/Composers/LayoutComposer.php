<?php

namespace App\Http\Composers;

use App\Entities\Notification\Notification;
use Illuminate\Contracts\View\View;

class LayoutComposer
{
    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $analytics = '';
        $betaAlert = false;
        if (env('APP_PRODUCTION')) {
            $analytics = file_get_contents(__DIR__ . '/../../../analytics');
        }

        if (!env('APP_ALLOW')) {
            $betaAlert = true;
        }

        $notifications = null;
        if ($user = \Auth::user()) {
            $notifications = Notification::where('user', $user->id)
                ->where('read_at', null)
                ->orderBy('id', 'DESC')
                ->limit(20)
                ->get();
        }

        $allowPwa = null;
        if (config('app.env') == 'production') {
            $allowPwa = true;
        }

        $view->with([
            'analytics' => $analytics,
            'betaAlert' => $betaAlert,
            'notifications' => $notifications,
            'allowPwa' => $allowPwa
        ]);
    }
}
