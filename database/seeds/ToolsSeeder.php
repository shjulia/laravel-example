<?php

declare(strict_types=1);

use App\Entities\Data\Tool;
use Illuminate\Database\Seeder;

/**
 * Class ToolsSeeder
 */
class ToolsSeeder extends Seeder
{
    public function run()
    {
        $tools = ['Eaglesoft', 'Dentrix', 'OpenDental', 'Mac Practice', 'Softdent'];
        foreach ($tools as $tool) {
            $tool = Tool::createRegular($tool);
            $tool->save();
        }
    }
}
