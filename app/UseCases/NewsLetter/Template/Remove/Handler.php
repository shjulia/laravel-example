<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter\Template\Remove;

use App\Entities\NewsLetter\Template;

class Handler
{
    public function handle(Command $command): void
    {
        /** @var Template $template */
        $template = Template::where('id', $command->id)->first();
        $template->delete();
    }
}
