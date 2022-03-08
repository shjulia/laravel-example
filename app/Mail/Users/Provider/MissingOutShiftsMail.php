<?php

declare(strict_types=1);

namespace App\Mail\Users\Provider;

use App\Entities\User\Provider\Specialist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class MissingOutShiftsMail
 * @package App\Mail\Users\Provider
 */
class MissingOutShiftsMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * @var Specialist
     */
    public $provider;

    /**
     * @var float
     */
    public $sum;

    /**
     * @var int
     */
    public $amount;

    public function __construct(Specialist $provider, float $sum, int $amount)
    {
        $this->provider = $provider;
        $this->sum = $sum;
        $this->amount = $amount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("You're Missing Out")
            ->view('emails.users.provider.missing-out-shifts');
    }
}
