<?php

declare(strict_types=1);

namespace App\Mail\Shift\MonthlySummary;

use App\Entities\User\Practice\Practice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlySummaryPracticeMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var array
     */
    private $shiftGroup;
    /**
     * @var Practice
     */
    private $practice;

    public function __construct(Practice $practice, array $shiftGroup)
    {
        $this->shiftGroup = $shiftGroup;
        $this->practice = $practice;
    }

    public function build()
    {
    }
}
