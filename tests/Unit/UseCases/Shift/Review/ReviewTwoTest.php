<?php

namespace Tests\Unit\UseCases\Shift\Review;

use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Fake\Services\Wallet\Practice\FakeWalletService;
use App\Http\Requests\Review\ReviewToPracticeRequest;
use App\Http\Requests\Review\ReviewToProviderRequest;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Payment\ChargeRepository;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\User\SpecialistRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Review\Provider\ReviewService as ProviderReviewService;
use App\UseCases\Review\Practice\ReviewService as PracticeReviewService;
use App\UseCases\Shift\CostService;
use App\UseCases\Shift\MatchingService;
use App\UseCases\Shift\Provider\ShiftService as ProviderShiftService;
use App\UseCases\Shift\ShiftPaymentService;
use App\UseCases\Shift\ShiftService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Shift\ShiftTrait;
use Tests\TestCase;

/**
 * Class ReviewTwoTest
 * @package Tests\Unit\Shift\Review
 */
class ReviewTwoTest extends TestCase
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
     * @var ProviderShiftService
     */
    private $providerService;

    /**
     * @var ProviderReviewService
     */
    private $providerReviewService;

    /**
     * @var PracticeReviewService
     */
    private $practiceReviewService;

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

        $this->providerReviewService = app(ProviderReviewService::class);
        $this->practiceReviewService = app(PracticeReviewService::class);
        /** @var User testUser */
        $this->testUser = $this->userRepository->findByEmailAndRole('practicetest1@gmail.com');
    }

    public function testFinisherShifts1()
    {
        $shift = $this->getNewShift();
        $shift = $this->match($shift);
        $potentialProvider = $shift->potentialProvider;
        $shift = $this->acceptByProvider($shift);
        $practice = $this->testUser->practice;
        $data = new ReviewToPracticeRequest();
        $data->merge([
            'text' => "good",
            'score' => 5,
            'score_marks' => ''
        ]);
        $this->providerReviewService->createReviewToPractice(clone $potentialProvider, $shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $data = new ReviewToProviderRequest();
        $data->merge([
            'text' => "good",
            'score' => 5,
            'score_marks' => ''
        ]);
        $this->assertTrue($shift->isAcceptedByProviderStatus());
        $this->practiceReviewService->createReviewToProvider(clone $practice, $shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->assertTrue($shift->isFinishedStatus());
    }

    public function testFinisherShifts2()
    {
        $shift = $this->getNewShift();
        $shift = $this->match($shift);
        $potentialProvider = $shift->potentialProvider;
        $shift = $this->acceptByProvider($shift);
        $practice = $this->testUser->practice;
        $data = new ReviewToProviderRequest();
        $data->merge([
            'text' => "good",
            'score' => 5,
            'score_marks' => ''
        ]);
        $this->practiceReviewService->createReviewToProvider(clone $practice, $shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->assertTrue($shift->isAcceptedByProviderStatus());
        $data = new ReviewToPracticeRequest();
        $data->merge([
            'text' => "good",
            'score' => 5,
            'score_marks' => ''
        ]);
        $this->providerReviewService->createReviewToPractice(clone $potentialProvider, $shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        $this->assertTrue($shift->isFinishedStatus());
    }
}
