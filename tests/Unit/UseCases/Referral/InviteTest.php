<?php
namespace Tests\Unit\UseCases\Referral;

use App\Entities\DTO\UserBase;
use App\Entities\Invite\Invite;
use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Http\Requests\Auth\Partner\UserDetailsRequest;
use App\Repositories\Invite\InviteRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Partner\RegisterService;
use App\UseCases\Auth\UserCreatorService;
use App\UseCases\Invite\InviteService;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InviteTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var InviteService
     */
    private $service;

    /**
     * @var \Faker\Generator
     */
    private $faker;
    /**
     * @var User
     */
    private $testUser;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var InviteRepository
     */
    private $inviteRepository;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->service = new InviteService(app(InviteRepository::class), new Dispatcher());
        $this->faker = Factory::create();
        $this->userRepository = app(UserRepository::class);
        $this->inviteRepository = app(InviteRepository::class);
    }

    public function setUp()
    {
        parent::setUp();
        $this->testUser = $this->registerPartner($this->faker->safeEmail);
    }

    public function testInvite()
    {
        $referral = $this->testUser->referral;
        $this->service->invite($inviteEmail = $this->faker->safeEmail, $referral);
        /** @var Invite $invite */
        $invite = $referral->invites()->where('email', $inviteEmail)->first();
        $this->assertNotNull($invite);
        $this->assertNull($invite->user_id);
        $this->assertTrue($invite->isNotAccepted());
        $this->assertFalse($invite->isAccepted());
    }

    public function testInviteAcception()
    {
        $referral = $this->testUser->referral;
        $this->service->invite($inviteEmail = $this->faker->safeEmail, $referral);
        /** @var Invite $invite */
        $invite = $referral->invites()->where('email', $inviteEmail)->first();
        $newUser = $this->registerPartner($inviteEmail, $referral->referral_code);
        $invite = $this->inviteRepository->getInviteById($invite->id);
        $this->assertNotNull($invite);
        $this->assertNotNull($invite->user_id);
        $this->assertFalse($invite->isNotAccepted());
        $this->assertTrue($invite->isAccepted());
        $this->assertEquals($newUser->id, $invite->user_id);
    }

    public function testResendInvite()
    {
        $referral = $this->testUser->referral;
        $this->service->invite($inviteEmail = $this->faker->safeEmail, $referral);
        /** @var Invite $invite */
        $invite = $referral->invites()->where('email', $inviteEmail)->first();
        $invite->update(['updated_at' => Carbon::now()->subDays(1)]);
        $updatedAt = $invite->updated_at;
        $this->service->resendInvite($referral, $invite);
        $invite = $this->inviteRepository->getInviteById($invite->id);
        $this->assertNotEquals($updatedAt->format('U'), $invite->updated_at->format('U'));
    }

    public function testChangeCode()
    {
        $newCode = 'somecode';
        $this->service->changeCode($this->testUser->referral, $newCode);
        $user = $this->userRepository->getById($this->testUser->id);
        $this->assertEquals($newCode, $user->referral->referral_code);
    }

    private function registerPartner(string $email, ?string $code = null): User
    {
        $userCreatorService = app(UserCreatorService::class);
        $registerService = new RegisterService(
            $userCreatorService,
            new Dispatcher()
        );
        $data = new UserBase(
            'first_name',
            'last_name',
            $email,
            null,
            null,
            null,
            $code
        );
        /** @var User $user */
        $user = $userCreatorService->createUser($data);
        $data = new UserDetailsRequest();
        $data->merge([
            'description' => 'sales',
            'description_answer' => "answer"
        ]);
        $registerService->detailsSave($data, $user);
        return $this->userRepository->getById($user->id);
    }
}
