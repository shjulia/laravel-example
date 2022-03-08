<?php

namespace Tests\Unit\UseCases\Register\Provider;

use App\Entities\Data\LicenseType;
use App\Entities\DTO\UserBase;
use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Fake\Services\Driver\DriverLicense\Photo\FakeAddService;
use App\Fake\Services\Driver\FakeCreateService;
use App\Fake\Services\Driver\FakeSSNService;
use App\Fake\Services\Wallet\Provider\FakeWalletService;
use App\Http\Requests\Auth\Provider\CheckRequest;
use App\Http\Requests\Auth\Provider\IdentityRequest;
use App\Http\Requests\Auth\Provider\IndustryRequest;
use App\Http\Requests\Auth\Provider\LicenceRequest;
use App\Repositories\Data\LicenseTypesRepository;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\User\SpecialistRepository;
use App\Repositories\User\UserRepository;
use App\Services\ImageAnalysis\DriverLicenseAnalysisAI;
use App\UseCases\Auth\Provider\RegisterService;
use App\UseCases\Auth\UserCreatorService;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class RegisterTest
 * @package Tests\Unit\Register\Provider
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
            app(SpecialistRepository::class),
            new Dispatcher(),
            app(FakeCreateService::class),
            app(FakeAddService::class),
            app(FakeSSNService::class),
            app(FakeWalletService::class)
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
        $data->toProvider();
        /** @var User testUser */
        $this->testUser = $userCreatorService->createUser($data);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testUserBase(): void
    {
        $positions = app()->get(PositionRepository::class);
        $position = $positions->getByTitle('Dentist');
        $data = new IndustryRequest();
        $data->merge([
            'industry' => $industry = $position->industry_id,
            'position' => $position = $position->id
        ]);
        $this->registerService->industrySave($data, $this->testUser);
        $this->assertEquals($industry, $this->testUser->specialist->industry_id);
        $this->assertEquals($position, $this->testUser->specialist->position_id);
        $this->testUser = $this->userRepository->getById($this->testUser->id);
        $this->assertEquals('provider:identity', $this->testUser->signup_step);
    }

    public function testIdentitySave(): void
    {
        $data = new IdentityRequest();
        $data->merge([
            'first_name' => 'first',
            'last_name' => 'last',
            'middle_name' => 'm',
            'address' => 'address',
            'city' => 'city',
            'state' => 'state',
            'zip' => 'zip',
            'dob' => '1995-08-04',
            'expiration_date' => '2020-12-12',
            'gender' => 'M',
            'license' => 'license_number'
        ]);
        $this->registerService->identitySave($data, $this->testUser);
        $this->testUser = $this->userRepository->getById($this->testUser->id);
        $this->assertEquals('provider:license', $this->testUser->signup_step);
        $this->assertNotNull($this->testUser->specialist->driver_state);
        $this->assertNotNull($this->testUser->specialist->driver_city);
        $this->assertNotNull($this->testUser->specialist->driver_zip);
        $this->assertNotNull($this->testUser->specialist->driver_license_number);
    }

    public function testMedicalLicenses(): void
    {
        $data = new LicenceRequest();
        $data->merge([
            'type' => LicenseType::limit(2)->pluck('id')->toArray(),
            'state' => ['GE', 'GE'],
            'number' => ['L12121212', 'L342423423'],
            'expiration_date' => ['2020-04-04', '2022-06-06'],
            'position' => [0, 1]
        ]);
        $this->registerService->licenseSave($data, $this->testUser);
        $this->assertNotEmpty($this->testUser->specialist->licenses);
        $this->assertEquals(2, $this->testUser->specialist->licenses->count());
        $this->testUser = $this->userRepository->getById($this->testUser->id);
        $this->assertEquals('provider:check', $this->testUser->signup_step);
    }

    public function testSsn(): void
    {
        $data = new CheckRequest();
        $ssn = '111112001';
        $data->merge([
            'ssn' => $ssn
        ]);
        $this->registerService->checkSave($data, $this->testUser);
        $this->testUser = $this->userRepository->getById($this->testUser->id);
        $this->assertNotNull($this->testUser->specialist->ssn);
        $this->assertNotEquals($ssn, $this->testUser->specialist->ssn);
    }

    public function testLastStep(): void
    {
        $this->registerService->lastStep($this->testUser);
        $this->assertNull($this->testUser->tmp_token);
        $this->assertNull($this->testUser->signup_step);
    }
}
