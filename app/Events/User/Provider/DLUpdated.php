<?php

declare(strict_types=1);

namespace App\Events\User\Provider;

use App\Entities\User\Provider\Specialist;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DLUpdated
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var Specialist
     */
    public $provider;

    public function __construct(Specialist $provider)
    {
        $this->provider = $provider;
    }
}
