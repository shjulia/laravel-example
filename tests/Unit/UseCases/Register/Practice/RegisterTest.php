<?php

namespace Tests\Unit\UseCases\Register\Practice;

use App\Entities\DTO\UserBase;
use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Http\Requests\Auth\Practice\BaseInfoRequest;
use App\Http\Requests\Auth\Practice\IndustryRequest;
use App\Http\Requests\Auth\Practice\InsuranceRequest;
use App\Repositories\User\PracticeRepository;
use App\Repositories\User\UserRepository;
use App\Services\Maps\AutocompletePlaceService;
use App\UseCases\Auth\Practice\RegisterService;
use App\UseCases\Auth\UserCreatorService;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class RegisterTest
 * @package Tests\Unit\Register\Practice
 */
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
            app(AutocompletePlaceService::class),
            app(PracticeRepository::class),
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
        $data->toPractice();
        /** @var User testUser */
        $this->testUser = $userCreatorService->createUser($data);
    }

    public function testPracticeIndustry(): void
    {
        $industry = 1;
        $data = new IndustryRequest();
        $data->merge([
            'industry' => $industry
        ]);
        $this->registerService->saveIndustry($data, $this->testUser);
        $this->assertEquals($industry, $this->testUser->practice->industry_id);
        $this->testUser = $this->userRepository->getById($this->testUser->id);
        $this->assertEquals('practice:base', $this->testUser->signup_step);
    }

    public function testBaseInfo(): void
    {
        $data = new BaseInfoRequest();
        $data->merge([
            'address' => 'Address',
            'city' => 'Atlanta',
            'state' => 'GA',
            'zip' => '12345',
            'name' => 'Practice name',
            'url' => 'https://127.0.0.1',
            'phone' => '+380000000000'
        ]);
        $this->registerService->saveBaseInfo($data, $this->testUser);
        $this->assertNotNull($this->testUser->practice->state);
        $this->assertNotNull($this->testUser->practice->city);
        $this->assertNotNull($this->testUser->practice->zip);
        $this->assertNotNull($this->testUser->practice->practice_name);
        $this->testUser = $this->userRepository->getById($this->testUser->id);
        $this->assertEquals('practice:insurance', $this->testUser->signup_step);
    }

    public function testSaveInsurance()
    {
        $data = new InsuranceRequest();
        $data->merge([
            'type' => 'type',
            'number' => 'N112343123',
            'expiration_date' => '2019-05-12',
            'policy_provider' => 'provider',
            'no_policy' => false
        ]);
        $this->registerService->saveInsurance($data, $this->testUser);
        $this->assertNotNull($this->testUser->practice->policy_number);
        $this->assertFalse((bool)$this->testUser->practice->no_policy);
        $this->testUser = $this->userRepository->getById($this->testUser->id);
        $this->assertNull($this->testUser->signup_step);
        $this->assertNull($this->testUser->tmp_token);
    }
}
