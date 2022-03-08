<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter\NewsLetter\Finish;

use App\Entities\NewsLetter\NewsLetter;

class Handler
{
    public function handle(Command $command): void
    {
        /** @var NewsLetter $newsLetter */
        $newsLetter = NewsLetter::where('id', $command->id)->firstOrFail();
        $newsLetter->finish();
        $newsLetter->saveOrFail();
    }
}
