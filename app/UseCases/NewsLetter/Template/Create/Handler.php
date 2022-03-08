<?php

declare(strict_types=1);

namespace App\UseCases\NewsLetter\Template\Create;

use App\Entities\NewsLetter\Template;

class Handler
{
    public function handle(Command $command): void
    {
        $template = Template::create(
            $command->title,
            $command->html_content,
            $command->json_content
        );
        $template->saveOrFail();
    }
}
