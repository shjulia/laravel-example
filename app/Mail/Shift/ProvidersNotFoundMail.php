<?php

namespace App\Mail\Shift;

use App\Entities\Shift\Shift;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProvidersNotFoundMail
 * @package App\Mail\Shift
 */
class ProvidersNotFoundMail extends Mailable
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
        return $this->markdown('vendor.notifications.email')->with([
            "level" => "default",
            "greeting" => "Hi, Admin",
            "introLines" => [
                "No providers found in time for shift " . $this->shift->id
            ],
            "actionText" => "See shift",
            "actionUrl" => route('admin.shifts.show', $this->shift),
            "outroLines" => [""]
        ]);
    }
}
