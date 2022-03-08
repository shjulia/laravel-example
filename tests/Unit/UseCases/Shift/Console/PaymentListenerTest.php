<?php
namespace Tests\Unit\UseCases\Shift\Console;

use App\Entities\Invite\Invite;
use App\Entities\Shift\Shift;
use App\Entities\User\Referral;
use App\Events\Shift\PaymentEvent;
use App\Fake\Event\Dispatcher;
use App\Fake\Services\Wallet\Practice\FakeWalletService;
use App\Listeners\Shift\PaymentListener;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\Invite\InviteRepository;
use App\Repositories\Payment\ChargeRepository;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\User\PracticeRepository;
use App\Repositories\User\SpecialistRepository;
use App\Repositories\User\UserRepository;
use App\Services\Notifications\Shifts\NotifyService;
use App\UseCases\Admin\Notifications\PaymentsProblemService;
use App\UseCases\Shift\CostService;
use App\UseCases\Shift\MatchingService;
use App\UseCases\Shift\PaymentService;
use App\UseCases\Shift\Provider\ShiftService as ProviderShiftService;
use App\UseCases\Shift\ShiftPaymentService;
use App\UseCases\Shift\ShiftService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Testing\Fakes\MailFake;
use Tests\Builders\Shift\ShiftTrait;
use Tests\TestCase;

/**
 * Class PaymentListenerTest
 * @package Tests\Unit\Shift\Console
 */
class PaymentListenerTest extends TestCase
{
    use ShiftTrait;
    use DatabaseTransactions;

    /**
     * @var Shift
     */
    private $shift;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var PaymentListener
     */
    private $service;
    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;
    /**
     * @var PracticeRepository
     */
    private $practiceRepository;
    /**
     * @var ShiftService
     */
    private $shiftService;
    /**
     * @var \App\Entities\User\User
     */
    private $testUser;
    /**
     * @var ShiftRepository
     */
    private $shiftRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
        $this->testUser = $this->userRepository->findByEmailAndRole('practicetest1@gmail.com');
        $this->shiftRepository = app(ShiftRepository::class);
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
        $shift = $this->getNewShift();
        $shift = $this->match($shift);
        $this->shift = $this->acceptByProvider($shift);
        $this->specialistRepository = app(SpecialistRepository::class);
        $this->practiceRepository = app(PracticeRepository::class);
        $this->service = new PaymentListener(
            app(PaymentService::class),
            new NotifyService(new MailFake()),
            new PaymentsProblemService($this->userRepository, new MailFake()),
            $this->specialistRepository,
            app(InviteRepository::class)
        );
    }

    public function testPaymentsWithInvite()
    {
        $provider = $this->shift->provider;
        $creator = $this->shift->creator;
        Invite::create([
            'referral_id' => $creator->id,
            'email' => $provider->user->email,
            'user_id' => $provider->user_id,
            'accepted' => Invite::ACCEPTED
        ]);
        $this->service->handle(new PaymentEvent($this->shift));
        $this->assertEquals(Referral::REFERRAL_FEE, $creator->referral->referral_money_earned);
    }

}
