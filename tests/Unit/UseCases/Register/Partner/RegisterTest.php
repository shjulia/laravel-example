<?php

namespace Tests\Unit\UseCases\Register\Partner;

use App\Entities\DTO\UserBase;
use App\Entities\User\Role;
use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Http\Requests\Auth\Partner\UserDetailsRequest;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Partner\RegisterService;
use App\UseCases\Auth\UserCreatorService;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var RegisterService
     */
    private $registerService;

    /**
     * @var Generator
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

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Factory::create();
        $this->userRepository = app(UserRepository::class);
    }

    public function setUp()
    {
        parent::setUp();
        $userCreatorService = app(UserCreatorService::class);
        $this->registerService = new RegisterService(
            $userCreatorService,
            new Dispatcher()
        );
        $data = new UserBase(
            'first_name',
            'last_name',
            $this->faker->safeEmail,
            null,
            null,
            null,
            null
        );
        /** @var User testUser */
        $this->testUser = $userCreatorService->createUser($data);
    }

    public function testDetailsInfoProvider()
    {
        $data = new UserDetailsRequest();
        $data->merge([
            'description' => 'provider',
            'description_answer' => "yes"
        ]);
        $step = $this->registerService->detailsSave($data, $this->testUser);
        $this->assertNotEmpty($step);
        $user = $this->userRepository->getById($this->testUser->id);
        $this->assertTrue($user->isProvider());
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->isAdmin());
        $this->assertEquals('provider:industry', $user->signup_step);
        $this->assertTrue($user->isWait());
        $specialist = $user->specialist;
        $this->assertNotNull($specialist);
        $this->assertNotNull($specialist->checkr);
        $this->assertNotNull($specialist->user->referral);
        $this->assertNotNull($user->referral->referral_code);
        $this->assertEquals(0, $user->referral->referral_money_earned);
        $this->assertEquals(0, $user->referral->referred_amount);
        $this->assertNull($user->partner);
        $this->assertNotNull($user->specialist->industry_id);
    }

    public function testDetailsInfoPractice()
    {
        $data = new UserDetailsRequest();
        $data->merge([
            'description' => 'practice',
            'description_answer' => "yes"
        ]);
        $step = $this->registerService->detailsSave($data, $this->testUser);
        $this->assertNotEmpty($step);
        $user = $this->userRepository->getById($this->testUser->id);
        $this->assertTrue($user->isPractice());
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->isAdmin());
        $this->assertEquals('practice:base', $user->signup_step);
        $this->assertTrue($user->isWait());
        $this->assertNotNull($user->referral);
        $this->assertNotNull($user->referral->referral_code);
        $this->assertEquals(0, $user->referral->referral_money_earned);
        $this->assertEquals(0, $user->referral->referred_amount);
        $practice = $user->practice;
        $this->assertNotNull($practice);
        $this->assertTrue((bool)$practice->pivot->is_creator);
        $this->assertEquals(Role::PRACTICE_ADMINISTRATOR, $practice->pivot->practice_role);
        $this->assertNull($user->partner);
        $this->assertNotNull($practice->industry_id);
    }

    public function testDetailsInfoPartner()
    {
        $data = new UserDetailsRequest();
        $data->merge([
            'description' => 'sales',
            'description_answer' => "answer"
        ]);
        $step = $this->registerService->detailsSave($data, $this->testUser);
        $this->assertNotEmpty($step);
        $user = $this->userRepository->getById($this->testUser->id);
        $this->assertTrue($user->isPartner());
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isPractice());
        $this->assertFalse($user->isProvider());
        $this->assertNull($user->signup_step);
        $this->assertNull($user->tmp_token);
        $this->assertTrue($user->isWait());
        $this->assertNotNull($user->partner);
        $this->assertNotNull($user->referral);
        $this->assertNotNull($user->referral->referral_code);
        $this->assertEquals(0, $user->referral->referral_money_earned);
        $this->assertEquals(0, $user->referral->referred_amount);
    }

    public function testDetailsInfoProviderWithNoCreation()
    {
        $data = new UserDetailsRequest();
        $data->merge([
            'description' => 'provider',
            'description_answer' => null
        ]);
        $this->registerService->detailsSave($data, $this->testUser);
        $user = $this->userRepository->getById($this->testUser->id);
        $this->assertFalse($user->isProvider());
        $this->assertNull($user->signup_step);
        $this->assertNull($user->tmp_token);
    }

    public function testDetailsInfoPracticeWithNoCreation()
    {
        $data = new UserDetailsRequest();
        $data->merge([
            'description' => 'practice',
            'description_answer' => null
        ]);
        $this->registerService->detailsSave($data, $this->testUser);
        $user = $this->userRepository->getById($this->testUser->id);
        $this->assertFalse($user->isPractice());
        $this->assertNull($user->signup_step);
        $this->assertNull($user->tmp_token);
    }
}
