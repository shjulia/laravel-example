<?php
namespace Tests\Unit\UseCases\Register;

use App\Entities\DTO\UserBase;
use App\Entities\User\Role;
use App\Entities\User\User;
use App\UseCases\Auth\UserCreatorService;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Faker\Generator;

/**
 * Class BaseRegisterTest
 * @package Tests\Unit\Register
 */
class BaseRegisterTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var UserCreatorService
     */
    private $userCreatorService;

    /**
     * @var Generator
     */
    private $faker;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->userCreatorService = app(UserCreatorService::class);
        $this->faker = Factory::create();
    }

    public function testProviderCreation(): void
    {
        $email = $this->faker->safeEmail;
        $data = new UserBase(
            'first_name',
            'last_name',
            $email,
            null,
            null,
            null,
            null
        );
        $data->toProvider();
        $this->assertTrue($data->isProvider());
        /** @var User $user */
        $user = $this->userCreatorService->createUser($data);
        $this->assertNotEmpty($user);
        $this->assertIsInt($user->id);
        $this->assertTrue($user->isProvider());
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->isAdmin());
        $this->assertEquals($email, $user->email);
        $this->assertEquals('provider:industry', $user->signup_step);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->tmp_token);
        $this->assertTrue($user->isWait());
        $specialist = $user->specialist;
        $this->assertNotNull($specialist);
        $this->assertNotNull($specialist->checkr);
        $this->assertNotNull($specialist->user->referral);
    }

    public function testPracticeCreation(): void
    {
        $email = $this->faker->safeEmail;
        $data = new UserBase(
            'first_name',
            'last_name',
            $email,
            null,
            null,
            null,
            null
        );
        $data->toPractice();
        $this->assertTrue($data->isPractice());
        /** @var User $user */
        $user = $this->userCreatorService->createUser($data);
        $this->assertNotEmpty($user);
        $this->assertIsInt($user->id);
        $this->assertTrue($user->isPractice());
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->isAdmin());
        $this->assertEquals($email, $user->email);
        $this->assertEquals('practice:base', $user->signup_step);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->tmp_token);
        $this->assertTrue($user->isWait());
        $practice = $user->practice;
        $this->assertNotNull($practice);
        $this->assertTrue((bool)$practice->pivot->is_creator);
        $this->assertEquals(Role::PRACTICE_ADMINISTRATOR, $practice->pivot->practice_role);
    }

    public function testPartnerCreation(): void
    {
        $email = $this->faker->safeEmail;
        $data = new UserBase(
            'first_name',
            'last_name',
            $email,
            null,
            null,
            null,
            null
        );
        $this->assertFalse($data->isProvider());
        $this->assertFalse($data->isPractice());
        /** @var User $user */
        $user = $this->userCreatorService->createUser($data);
        $this->assertNotEmpty($user);
        $this->assertIsInt($user->id);
        $this->assertFalse($user->isProvider());
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isPractice());
        $this->assertEquals($email, $user->email);
        $this->assertEquals('base:details', $user->signup_step);
        $this->assertNotNull($user->password);
        $this->assertNotNull($user->tmp_token);
        $this->assertTrue($user->isWait());
        $this->assertNotNull($user->referral()->first());
    }
}
