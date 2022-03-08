<?php

declare(strict_types=1);

namespace App\Mail\Shift\Bonus;

use App\Entities\User\Provider\Specialist;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class RoadWarriorMail
 * @package App\Mail\Shift\Bonus
 */
class RoadWarriorMail extends Mailable implements ShouldQueue
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
    public $bonus;
    /**
     * @var float
     */
    public $distance;

    /**
     * RoadWarriorMail constructor.
     * @param Specialist $provider
     * @param float $bonus
     * @param float $distance
     */
    public function __construct(Specialist $provider, float $bonus, float $distance)
    {
        $this->provider = $provider;
        $this->bonus = $bonus;
        $this->distance = $distance;
    }

    /**
     * @return RoadWarriorMail
     */
    public function build()
    {
        return $this->subject('You\'re a Road Warrior')
            ->view('emails.shift.bonus.road-warrior');
    }
}
