<?php

declare(strict_types=1);

namespace Tests\Builders\Data;

use App\Entities\Data\State;

/**
 * Class StateBuilder
 * @package Tests\Builders\Data
 */
class StateBuilder
{
    /**
     * @var State
     */
    private $state;

    public function __construct()
    {
        $this->state = new State();
        $this->state->id = mt_rand();
        $this->state->title = str_random(8);
        $this->state->short_title = str_random(2);
    }

    /**
     * @return State
     */
    public function build(): State
    {
        return $this->state;
    }
}
