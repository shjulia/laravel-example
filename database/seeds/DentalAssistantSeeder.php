<?php
declare(strict_types=1);

use App\Entities\Industry\Industry;
use Illuminate\Database\Seeder;
use App\Entities\Industry\Position;

/**
 * Class DentalAssistantSeeder
 */
class DentalAssistantSeeder extends Seeder
{
    public function run()
    {
        $position = Position::where('title', 'Dental Assistant')->first();
        $industry = Industry::where('title', 'Dental')->first();
        foreach (['Dental Assistant 1', 'Dental Assistant 2'] as $title) {
            Position::create([
                'title' => $title,
                'industry_id' => $industry->id,
                'parent_id' => $position->id,
                'fee' => 16.71,
                'minimum_profit' => 8
            ]);
        }
    }
}
