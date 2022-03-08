<?php

namespace Tests\Unit\UseCases\Shift\Practice;

use App\Entities\Shift\Shift;
use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Fake\Services\Wallet\Practice\FakeWalletService;
use App\Http\Requests\Shift\ShiftBaseRequest;
use App\Http\Requests\Shift\TimeRequest;
use App\Jobs\Shift\MatchNowJob;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Payment\ChargeRepository;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Practice\DetailsService;
use App\UseCases\Shift\CostService;
use App\UseCases\Shift\MatchingService;
use App\UseCases\Shift\ShiftPaymentService;
use App\UseCases\Shift\ShiftService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class PracticeShiftTest
 * @package Tests\Unit\Shift
 */
class PracticeShiftTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var DetailsService
     */
    private $service;

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
     * @var PositionRepository
     */
    private $positionRepository;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->userRepository = app(UserRepository::class);
        $this->shiftRepository = app(ShiftRepository::class);
        $this->positionRepository = app(PositionRepository::class);
    }

    public function setUp()
    {
        parent::setUp();
        $this->service = new ShiftService(
            app(ShiftRepository::class),
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
        /** @var User testUser */
        $this->testUser = $this->userRepository->findByEmailAndRole('practicetest1@gmail.com');
    }

    public function testBase()
    {
        $position = $this->positionRepository->getByTitle('Dentist')->id;
        $data = new ShiftBaseRequest();
        $data->merge([
            'position' => $position
        ]);
        /** @var Shift $shift */
        $shift = $this->service->createBase($this->testUser->practice, $this->testUser, $data, true);
        $this->assertNotNull($shift->id);
        $this->assertTrue($shift->isCreatingStatus());
        $this->assertEquals($position, $shift->position->id);
        $this->assertEquals(Carbon::today(), $shift->date);
        $data = new ShiftBaseRequest();
        $data->merge([
            'position' => $position
        ]);
        $id = $shift->id;
        $shift = $this->service->createBase($this->testUser->practice, $this->testUser, $data, true, $shift);
        $this->assertEquals($id, $shift->id);
    }

    public function testTimeSetting()
    {
        $position = $this->positionRepository->getByTitle('Dentist')->id;
        $data = new ShiftBaseRequest();
        $data->merge([
            'position' => $position
        ]);
        $shift = $this->service->createBase($this->testUser->practice, $this->testUser, $data, true);
        $data = new TimeRequest();
        $timeFrom = '09:30';
        $timeTo = '18:30';
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d');
        $data->merge([
            'time_from' => $timeFrom,
            'time_to' => $timeTo,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'shift_time' => 540
        ]);
        $this->service->setTime($shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->assertEquals($timeFrom, $shift->from_time);
        $this->assertEquals($timeTo, $shift->to_time);
        $this->assertEquals($startDate, $shift->date);
        $this->assertEquals($endDate, $shift->end_date);
        $this->assertNotNull($shift->cost);
        $this->assertNotNull($shift->cost_for_practice);
        $this->assertTrue($shift->cost_for_practice > $shift->cost);
        $charge = $shift->charges()->orderBy('id', 'DESC')->first();
        $this->assertNotNull($charge);
        $this->assertEquals($shift->cost_for_practice, $charge->amount);
        $this->assertFalse((bool)$charge->is_capture);
        $this->assertFalse((bool)$charge->is_refund);
    }

    public function testStartMatching()
    {
        $position = $this->positionRepository->getByTitle('Dentist')->id;
        $data = new ShiftBaseRequest();
        $data->merge([
            'position' => $position
        ]);
        $shift = $this->service->createBase($this->testUser->practice, $this->testUser, $data, true);
        $data = new TimeRequest();
        $data->merge([
            'time_from' => '09:30',
            'time_to' => '18:30',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'shift_time' => 540
        ]);
        $this->service->setTime($shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->expectsJobs(MatchNowJob::class);
        $this->service->startMatching($shift);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->assertTrue($shift->isMatchingStatus());
    }

    public function testCanceling()
    {
        $position = $this->positionRepository->getByTitle('Dentist')->id;
        $data = new ShiftBaseRequest();
        $data->merge([
            'position' => $position
        ]);
        $shift = $this->service->createBase($this->testUser->practice, $this->testUser, $data, true);
        $data = new TimeRequest();
        $data->merge([
            'time_from' => '09:30',
            'time_to' => '18:30',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'shift_time' => 540
        ]);
        $this->service->setTime($shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->service->cancel($shift, 'to expensive', false);
        $this->assertTrue($shift->isCanceledStatus());
    }

    public function testNoProvidersInTime()
    {
        $position = $this->positionRepository->getByTitle('Dentist')->id;
        $data = new ShiftBaseRequest();
        $data->merge([
            'position' => $position
        ]);
        $shift = $this->service->createBase($this->testUser->practice, $this->testUser, $data, true);
        $data = new TimeRequest();
        $data->merge([
            'time_from' => '09:30',
            'time_to' => '18:30',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'shift_time' => 540
        ]);
        $this->service->setTime($shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->service->noProvidersInTime($shift);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->assertTrue($shift->isNoPrividerFoundStatus());
    }
}
