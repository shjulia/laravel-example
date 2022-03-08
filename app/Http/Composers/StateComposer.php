<?php

namespace App\Http\Composers;

use App\Repositories\Data\StatesRepository;
use Illuminate\Contracts\View\View;

/**
 * Class StateComposer
 * @package App\Http\Composers
 */
class StateComposer
{
    /**
     * @var StatesRepository
     */
    private $states;

    /**
     * StateComposer constructor.
     * @param StatesRepository $states
     */
    public function __construct(StatesRepository $states)
    {
        $this->states = $states;
    }

    public function compose(View $view)
    {
        $view->with('states', $this->states->getAll());
    }
}
