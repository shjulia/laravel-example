<?php

namespace Tests\Unit\UseCases\Register\Practice;

use App\Entities\DTO\UserBase;
use App\Entities\User\Role;
use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Fake\Services\Wallet\Practice\FakeWalletService;
use App\Http\Requests\Auth\Practice\Details\TeamMemberRequest;
use App\Repositories\User\RolesRepository;
use App\Repositories\User\UserRepository;
use App\UseCases\Auth\Practice\DetailsService;
use App\UseCases\Auth\UserCreatorService;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Tests\TestCase;

class DetailsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var DetailsService
     */
    private $service;

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
        $this->service = new DetailsService(
            $this->userRepository,
            app(RolesRepository::class),
            app()->get(Dispatcher::class),
            app()->get(FakeWalletService::class)
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
        $data->toPractice();
        /** @var User testUser */
        $this->testUser = $userCreatorService->createUser($data);
    }

    public function testTeamMemberSaving()
    {
        $data = new TeamMemberRequest();
        $data->merge([
            'first_name' => 'name',
            'last_name' => 'lname',
            'email' => 'email@email.com',
            'role' => Role::PRACTICE_HIRING_MANAGER
        ]);
        /** @var User $user */
        $user = $this->service->saveTeamMember($data, $this->testUser);
        $this->assertEquals($this->testUser->practice->id, $user->practice->id);
        $this->assertEquals(Role::PRACTICE_HIRING_MANAGER, $user->practice->pivot->practice_role);
        $this->assertFalse((bool)$user->practice->pivot->is_creator);
        $this->assertTrue($user->isPractice());
        $this->assertTrue($user->isWait());
        $this->service->deleteTeamMember($user->id, $this->testUser);
        $this->assertNull($user->practice);
    }

    public function testBillingSavingWithoutCard()
    {
        $data = new Request();
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Card data must be set');
        $this->service->billingSave($data, $this->testUser);
    }

    public function testBillingSaving()
    {
        $data = new Request();
        $data->merge([
            'token' => 'some_token'
        ]);
        $this->service->billingSave($data, $this->testUser);
        $this->assertTrue($this->testUser->wallet->has_payment_data);
    }
}
