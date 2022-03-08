<?php

declare(strict_types=1);

namespace App\Events\Maps\Geocoder;

use App\Entities\User\Practice\PracticeAddress;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PracticeAddressGeocodeEvent
 *
 * Listener {@see \App\Listeners\Maps\Geocoder\PracticeAddressGeocodeListener}
 * @package App\Events\Maps\Geocoder
 */
class PracticeAddressGeocodeEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var PracticeAddress
     */
    public $address;

    /**
     * PracticeAddressGeocodeEvent constructor.
     * @param PracticeAddress $address
     */
    public function __construct(PracticeAddress $address)
    {
        $this->address = $address;
    }
}
