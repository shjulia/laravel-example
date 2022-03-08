<?php

declare(strict_types=1);

namespace App\Events\Admin\Areas;

use App\Entities\Data\Location\Area;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class AreaEvent
 *
 * Listener @see \App\Listeners\Area\AreaListener
 * @package App\Events\Admin\Areas
 */
class AreaEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Area
     */
    public $area;

    /**
     * AreaEvent constructor.
     * @param Area $area
     */
    public function __construct(Area $area)
    {
        $this->area = $area;
    }
}
