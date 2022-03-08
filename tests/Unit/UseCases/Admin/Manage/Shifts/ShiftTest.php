<?php

namespace Tests\Unit\UseCases\Admin\Manage\Shifts;

use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Fake\Services\Wallet\Practice\FakeWalletService;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Payment\ChargeRepository;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\User\SpecialistRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Shift\CostService;
use App\UseCases\Shift\MatchingService;
use App\UseCases\Shift\ShiftPaymentService;
use App\UseCases\Shift\ShiftService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Shift\ShiftTrait;
use Tests\TestCase;

/**
 * Class ShiftTest
 * @package Tests\Unit\Admin\Manage\Shifts
 */
class ShiftTest extends TestCase
{
    use DatabaseTransactions;
    use ShiftTrait;

    /**
     * @var ShiftService
     */
    private $shiftService;

    /**
     * @var User
     */
    private $testUser;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var ShiftRepository
     */
    private $shiftRepository;

    /**
     * @var \App\UseCases\Admin\Manage\Shift\ShiftService
     */
    private $service;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->userRepository = app(UserRepository::class);
        $this->shiftRepository = app(ShiftRepository::class);
    }

    public function setUp()
    {
        parent::setUp();
        $this->shiftService = new ShiftService(
            $this->shiftRepository,
            app(PositionRepository::class),
            app(MatchingService::class),
            app(CostService::class),
            app(ChargeRepository::class),
            new Dispatcher(),
            new ShiftPaymentService(
                app(ChargeRepository::class),
                app()->get(FakeWalletService::class),
                new Dispatcher()
            )
        );

        $this->service = new \App\UseCases\Admin\Manage\Shift\ShiftService(
            new Dispatcher(),
            app(ChargeRepository::class),
            app(SpecialistRepository::class),
            app(ShiftService::class)
        );
        /** @var User testUser */
        $this->testUser = $this->userRepository->findByEmailAndRole('practicetest1@gmail.com');
    }

    public function testFinisherShifts()
    {
        $shift = $this->getNewShift(true);
        $shift = $this->match($shift);
        $this->assertTrue($shift->isNoPrividerFoundStatus());
        $provider = $this->userRepository->findByEmailAndRole('testprovider1@gmail.com');
        $this->service->editProvider($this->shiftRepository->getByIdOnlyShift($shift->id), $provider->id);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->assertEquals($provider->id, $shift->provider_id);
        $this->assertTrue($shift->isAcceptedByProviderStatus());
    }
}
