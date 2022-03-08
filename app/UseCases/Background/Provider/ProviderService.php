<?php

declare(strict_types=1);

namespace App\UseCases\Background\Provider;

use App\Entities\User\User;
use App\Events\User\Provider\LocationChangedEvent;
use App\Repositories\User\SpecialistRepository;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Class ProviderService
 * Saves provider's last location.
 *
 * @package App\UseCases\Background\Provider
 */
class ProviderService
{
    /**
     * @var SpecialistRepository
     */
    private $repository;
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * ProviderService constructor.
     * @param SpecialistRepository $repository
     * @param Dispatcher $dispatcher
     */
    public function __construct(SpecialistRepository $repository, Dispatcher $dispatcher)
    {
        $this->repository = $repository;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param User $user
     * @param float $lat
     * @param float $lng
     */
    public function markLastLocation(User $user, float $lat, float $lng): void
    {
        $provider = $user->specialist;
        $provider->markLastLocation($lat, $lng);
        $this->repository->save($provider);
        $this->dispatcher->dispatch(new LocationChangedEvent($provider));
    }
}
