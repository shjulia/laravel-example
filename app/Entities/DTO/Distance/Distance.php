<?php

declare(strict_types=1);

namespace App\Entities\DTO\Distance;

/**
 * Class Distance - transfer distance in several formats
 * @package App\Entities\DTO\Distance
 */
class Distance
{
    /**
     * @var float
     */
    public $distanceVal;

    /**
     * @var string
     */
    public $distanceText;

    /**
     * @var float
     */
    public $durationVal;

    /**
     * @var string
     */
    public $durationText;

    /**
     * Distance constructor.
     * @param float $distanceVal
     * @param string $distanceText
     * @param float $durationVal
     * @param string $durationText
     */
    public function __construct(float $distanceVal, string $distanceText, float $durationVal, string $durationText)
    {
        $this->distanceVal = $distanceVal;
        $this->distanceText = $distanceText;
        $this->durationVal = $durationVal;
        $this->durationText = $durationText;
    }
}
