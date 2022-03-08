<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter\NewsLetter\Finish;

class Command
{
    /**
     * @var int
     */
    public $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
