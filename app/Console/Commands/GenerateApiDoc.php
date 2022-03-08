<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class GenerateApiDoc
 * Generates documentation for API with Swagger.
 *
 * @package App\Console\Commands
 */
class GenerateApiDoc extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:doc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $swagger = base_path('vendor/bin/swagger');
        $source = app_path('');
        $target = public_path('docs/swagger.json');
        passthru('"' . PHP_BINARY . '"' . " \"{$swagger}\" \"{$source}\" --output \"{$target}\"");
    }
}
