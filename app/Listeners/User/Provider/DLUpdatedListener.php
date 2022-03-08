<?php

declare(strict_types=1);

namespace App\Listeners\User\Provider;

use App\Events\User\Provider\DLUpdated;
use App\Services\Auth\Provider\Driver\DriverLicense\EditService;
use Illuminate\Contracts\Queue\ShouldQueue;

class DLUpdatedListener implements ShouldQueue
{
    /**
     * @var EditService
     */
    private $editService;

    public function __construct(EditService $editService)
    {
        $this->editService = $editService;
    }
    public function handle(DLUpdated $event): void
    {
        $this->editService->edit($event->provider);
    }
}
