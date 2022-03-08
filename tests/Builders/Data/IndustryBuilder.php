<?php

declare(strict_types=1);

namespace Tests\Builders\Data;

use App\Entities\Industry\Industry;

/**
 * Class IndustryBuilder
 * @package Tests\Builders\Data
 */
class IndustryBuilder
{
    /**
     * @var Industry
     */
    private $industry;

    public function __construct()
    {
        $this->industry = Industry::createNew(str_random(), str_random());
        $this->industry->id = mt_rand();
    }

    /**
     * @return Industry
     */
    public function build(): Industry
    {
        return $this->industry;
    }
}
