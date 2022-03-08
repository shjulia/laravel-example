<?php

namespace Tests\Unit\UseCases\Register\Provider;

use App\Entities\DTO\UserBase;
use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Http\Requests\Auth\Provider\DetailsRequest;
use App\Repositories\User\SpecialistRepository;
use App\Repositories\User\UserRepository;
use App\Services\Wallet\Provider\WalletService;
use App\UseCases\Auth\Provider\DetailsService;
use App\UseCases\Auth\UserCreatorService;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Class DetailsTest
 * @package Tests\Unit\Register\Provider
 */
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
            app(SpecialistRepository::class),
            new Dispatcher(),
            app(WalletService::class)
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

    public function testDetailsSaving()
    {
        $data = new DetailsRequest();
        $specialities = [1,2];
        $from = ['06:00', '07:00', '08:00'];
        $to = ['10:00', '12:00', '15:00'];
        $days = [1,2,3];
        $holidays = [1 => true, 2 => true];
        $address = [
            'state' => 'GA',
            'city' => 'Atlanta',
            'zip' => '2131431',
            'address' => 'Address'
        ];
        $data->merge([
            'specialities' => $specialities,
            'routine_tasks' => [],
            'from' => $from,
            'to' => $to,
            'day' => $days,
            'delete' => true,
            'holiday' => $holidays,
            'state' => $address['state'],
            'city' => $address['city'],
            'zip' => $address['zip'],
            'address' => $address['address']
        ]);
        $this->service->saveDetails($data, $this->testUser);
        $provider = $this->testUser->specialist;
        $this->assertArraySubset($specialities, $provider->specialities->pluck('id')->toArray());
        $i = 0;
        foreach ($provider->availabilities as $availability) {
            $this->assertEquals($from[$i], $availability->from_hour);
            $this->assertEquals($to[$i], $availability->to_hour);
            $this->assertEquals($days[$i], $availability->day);
            $i++;
        }
        $this->assertArraySubset(array_keys($holidays), $provider->holidays->pluck('id')->toArray());
        $this->assertEquals($address['state'], $provider->driver_state);
        $this->assertEquals($address['city'], $provider->driver_city);
        $this->assertEquals($address['zip'], $provider->driver_zip);
        $this->assertEquals($address['address'], $provider->driver_address);
    }
}
