<?php

declare(strict_types=1);

namespace App\Mail\Shift\Cost;

use App\Entities\Shift\Shift;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ChangedForProvider
 * @package App\Mail\Shift\Cost
 */
class ChangedForProvider extends Mailable
{
    use SerializesModels;

    /**
     * @var Shift $shift
     */
    public $shift;

    /**
     * ChangedForProvider constructor.
     * @param Shift $shift
     */
    public function __construct(Shift $shift)
    {
        $this->shift = $shift;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Update - Shift ' . $this->shift->id)
            ->view('emails.shift.cost.changed-for-provider');
    }
}
