<?php

declare(strict_types=1);

namespace App\Mail\Shift\MonthlySummary;

use App\Entities\User\Provider\Specialist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MonthlySummaryProviderMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var Specialist
     */
    private $provider;
    /**
     * @var array
     */
    private $shiftGroup;

    public function __construct(Specialist $provider, array $shiftGroup)
    {
        $this->provider = $provider;
        $this->shiftGroup = $shiftGroup;
    }

    public function build()
    {
    }
}
