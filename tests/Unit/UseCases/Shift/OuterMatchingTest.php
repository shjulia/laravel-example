<?php

namespace Tests\Unit\UseCases\Shift;

use App\Entities\Shift\ShiftInvite;
use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Fake\Services\Wallet\Practice\FakeWalletService;
use App\Http\Requests\Shift\ShiftBaseRequest;
use App\Http\Requests\Shift\TimeRequest;
use App\Jobs\Shift\MatchNowJob;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Payment\ChargeRepository;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\User\SpecialistRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Shift\CostService;
use App\UseCases\Shift\MatchingService;
use App\UseCases\Shift\ShiftPaymentService;
use App\UseCases\Shift\ShiftService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;
use App\UseCases\Shift\Provider\ShiftService as ProviderShiftService;

/**
 * Class MatchingTest
 * @package Tests\Unit\Shift
 */
class OuterMatchingTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var ShiftService
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
     * @var \App\Entities\Shift\Shift
     */
    private $shift;

    /**
     * @var ProviderShiftService
     */
    private $providerService;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->userRepository = app(UserRepository::class);
        $this->shiftRepository = app(ShiftRepository::class);
    }

    public function setUp()
    {
        parent::setUp();
        $this->service = new ShiftService(
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
        $this->providerService = new ProviderShiftService(
            $this->shiftRepository,
            app(SpecialistRepository::class),
            new Dispatcher(),
            new ShiftPaymentService(
                app(ChargeRepository::class),
                app()->get(FakeWalletService::class),
                new Dispatcher()
            ),
            app(CostService::class)
        );
        /** @var User testUser */
        $this->testUser = $this->userRepository->findByEmailAndRole('practicetest1@gmail.com');
        $position = app(PositionRepository::class)->getByTitle('Dentist')->id;
        $data = new ShiftBaseRequest();
        $data->merge([
            'position' => $position
        ]);
        $shift = $this->service->createBase($this->testUser->practice, $this->testUser, $data, true);
        $data = new TimeRequest();
        $data->merge([
            'time_from' => '09:30',
            'time_to' => '18:30',
            'start_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'end_date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'shift_time' => 1800
        ]);
        $this->service->setTime($shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->expectsJobs(MatchNowJob::class);
        $this->service->startMatching($shift);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->assertTrue($shift->isMatchingStatus());
        $this->shift = $shift;
    }

    public function testMatch()
    {
        $job = new MatchNowJob($this->shift);
        $this->expectsJobs(MatchNowJob::class);
        $job->handle($this->service, $this->shiftRepository);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $this->assertNotNull($shift->potential_provider_id);
        $this->assertNotNull($shift->steps);
        $this->assertNotNull($shift->shiftInvites);
        $this->assertEquals($shift->potential_provider_id, $shift->shiftInvites[0]->provider_id);
        $this->assertEquals(ShiftInvite::NO_RESPOND, $shift->shiftInvites[0]->status);
    }

    public function testProviderAccept()
    {
        $job = new MatchNowJob($this->shift);
        $this->expectsJobs(MatchNowJob::class);
        $job->handle($this->service, $this->shiftRepository);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $data = new Request();
        $data->merge(['answer' => 'now']);
        $potentialProvider = $shift->potentialProvider;
        $this->providerService->accept($shift, $data, $potentialProvider);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $this->assertNotNull($shift->provider_id);
        $this->assertEquals($shift->provider_id, $potentialProvider->user_id);
        $this->assertEquals(ShiftInvite::ACCEPTED, $shift->shiftInvites[0]->status);
        $this->assertTrue($shift->isAcceptedByProviderStatus());
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Shift already accepted');
        $this->providerService->check($shift, $shift->provider);
    }

    public function testProviderDecline()
    {
        $job = new MatchNowJob($this->shift);
        $this->expectsJobs(MatchNowJob::class);
        $job->handle($this->service, $this->shiftRepository);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $potentialProvider = $shift->potentialProvider;
        $this->providerService->decline($shift, $potentialProvider);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $this->assertNull($shift->provider_id);
        $this->assertEquals(null, $shift->potential_provider_id);
        $this->assertEquals(ShiftInvite::DECLINED, $shift->shiftInvites[0]->status);
        $this->assertTrue($shift->isMatchingStatus());
    }

    public function testSeveralProvidersAnswers()
    {
        $job = new MatchNowJob($this->shift);
        $this->expectsJobs(MatchNowJob::class);
        $job->handle($this->service, $this->shiftRepository);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $firstPotentialProvider = $shift->potentialProvider;
        $this->assertEquals($shift->potential_provider_id, $firstPotentialProvider->user_id);
        $this->expectsJobs(MatchNowJob::class);
        $job->handle($this->service, $this->shiftRepository);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $secondPotentialProvider = $shift->potentialProvider;
        $data = new Request();
        $data->merge(['answer' => 'now']);
        $this->providerService->accept($shift, $data, $secondPotentialProvider);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $this->assertNotNull($shift->provider_id);
        $this->assertEquals($shift->provider_id, $secondPotentialProvider->user_id);
        $this->assertEquals(ShiftInvite::ACCEPTED, $shift->shiftInvites[1]->status);
        $this->assertTrue($shift->isAcceptedByProviderStatus());
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Another provider has already accepted this shift. You did not have time');
        $this->providerService->check($shift, $firstPotentialProvider);
    }

    public function testSeveralProvidersAnswers2()
    {
        $job = new MatchNowJob($this->shift);
        $this->expectsJobs(MatchNowJob::class);
        $job->handle($this->service, $this->shiftRepository);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $firstPotentialProvider = $shift->potentialProvider;
        $this->expectsJobs(MatchNowJob::class);
        $job->handle($this->service, $this->shiftRepository);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $secondPotentialProvider = $shift->potentialProvider;
        $data = new Request();
        $data->merge(['answer' => 'now']);
        $this->providerService->accept($shift, $data, $firstPotentialProvider);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $this->assertNotNull($shift->provider_id);
        $this->assertEquals($shift->provider_id, $firstPotentialProvider->user_id);
        $this->assertEquals(ShiftInvite::ACCEPTED, $shift->shiftInvites[0]->status);
        $this->assertTrue($shift->isAcceptedByProviderStatus());
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Another provider has already accepted this shift. You did not have time');
        $this->providerService->check($shift, $secondPotentialProvider);
    }

    public function testCanceledShift()
    {
        $job = new MatchNowJob($this->shift);
        $this->expectsJobs(MatchNowJob::class);
        $job->handle($this->service, $this->shiftRepository);
        $shift = $this->shiftRepository->getById($this->shift->id);
        $potentialProvider = $shift->potentialProvider;
        $this->service->cancel($shift, 'reason', false);
        $this->assertTrue($shift->isCanceledStatus());
        $shift = $this->shiftRepository->getById($this->shift->id);
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Practice already canceled shift');
        $this->providerService->check($shift, $potentialProvider);
    }
}
