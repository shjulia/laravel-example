<?php

namespace Tests\Unit\UseCases\Shift\Review;

use App\Entities\Review\Review;
use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Fake\Services\Wallet\Practice\FakeWalletService;
use App\Http\Requests\Review\ReviewToProviderRequest;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Payment\ChargeRepository;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\User\SpecialistRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Review\Practice\ReviewService;
use App\UseCases\Shift\CostService;
use App\UseCases\Shift\MatchingService;
use App\UseCases\Shift\Provider\ShiftService as ProviderShiftService;
use App\UseCases\Shift\ShiftPaymentService;
use App\UseCases\Shift\ShiftService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\Shift\ShiftTrait;
use Tests\TestCase;

/**
 * Class ProviderReviewTest (Test Review to provider from practice)
 * @package Tests\Unit\Shift\Review
 */
class ProviderReviewTest extends TestCase
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
     * @var ReviewService
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

        $this->service = app(ReviewService::class);
        /** @var User testUser */
        $this->testUser = $this->userRepository->findByEmailAndRole('practicetest1@gmail.com');
    }

    public function testFinisherShifts()
    {
        $shift = $this->getNewShift();
        $shift = $this->match($shift);
        $potentialProvider = $shift->potentialProvider;
        $shift = $this->acceptByProvider($shift);
        $practice = $this->testUser->practice;
        $data = new ReviewToProviderRequest();
        $text = "good";
        $score = 5;
        $data->merge([
            'text' => $text,
            'score' => $score,
            'score_marks' => ''
        ]);
        $this->service->createReviewToProvider(clone $practice, $shift, $data);
        $shift = $this->shiftRepository->getById($shift->id);
        /** @var Review $review */
        $review = $shift->reviewFromPractice[0];
        $this->assertTrue($shift->isHasReviewFromPractice());
        $this->assertEquals($text, $review->text);
        $this->assertEquals($score, $review->score);
        $this->assertEquals($potentialProvider->reviews_total + 1, $shift->provider->reviews_total);
        $this->assertEquals(round(($potentialProvider->average_stars * $potentialProvider->reviews_total
                + $review->score) / ($potentialProvider->reviews_total + 1), 2), $shift->provider->average_stars);
        $this->assertEquals($practice->reviews_to_provider_total + 1, $shift->practice->reviews_to_provider_total);
        $this->assertEquals(($practice->average_stars_to_provider * $practice->reviews_to_provider_total
                + $review->score) / ($practice->reviews_to_provider_total + 1),
            $shift->practice->average_stars_to_provider
        );
        $this->assertTrue($shift->isAcceptedByProviderStatus());
    }
}
