<?php

declare(strict_types=1);

namespace App\Mail\Shift\Support;

use App\Entities\Shift\Shift;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProviderRequestedMail
 * @package App\Mail\Shift\Support
 */
class ProviderRequestedMail extends Mailable
{
    use SerializesModels;

    /**
     * @var string
     */
    public $shift;

    /**
     * ProvidersNotFoundMail constructor.
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
        $shift = $this->shift;
        $html = "Provider requested. <br/>"
            . "<h5>Shift details</h5>"
            . "<br/><b>Practice: </b>" . $shift->practice->practice_name
            . "<br/><b>Datetime: </b>" . $shift->period()
            . "<br/><b>Position: </b>" . $shift->position->title
            . "<br/><b>Cost: </b>" . $shift->cost
            . "<br/><b>Cost for practice: </b>" . $shift->cost_for_practice
            . "<br/><b>Details: </b> <a href='" . route('admin.shifts.show', $this->shift) . "'>link</a>";
        return $this->subject('Provider requested')->html($html);
    }
}
