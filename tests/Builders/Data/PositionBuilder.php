<?php

declare(strict_types=1);

namespace Tests\Builders\Data;

use App\Entities\Industry\Industry;
use App\Entities\Industry\Position;

/**
 * Class PositionBuilder
 * @package Tests\Builders\Data
 */
class PositionBuilder
{
    /**
     * @var Position
     */
    private $position;

    public function __construct(?Industry $industry = null)
    {
        if (!$industry) {
            $industry = (new IndustryBuilder())->build();
        }
        $this->position = Position::createNew(
            str_random(),
            $industry,
            mt_rand(),
            mt_rand(),
            mt_rand()
        );
        $this->position->id = mt_rand();
    }

    /**
     * @return Position
     */
    public function build(): Position
    {
        return $this->position;
    }
}
