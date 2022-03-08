<?php

namespace App\Http\Composers;

use Illuminate\Contracts\View\View;

/**
 * Class DaysComposer
 * @package App\Http\Composers
 */
class DaysComposer
{
    /**
     * @var array
     */
    public const DAYS = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday'
    ];

    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $view->with('days', self::DAYS);
    }
}
