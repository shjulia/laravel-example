<?php
namespace Tests\Unit\UseCases\Admin\Manage\Users;

use App\Entities\User\Provider\Checkr;
use App\Fake\Event\Dispatcher;
use App\Repositories\User\SpecialistRepository;
use App\Repositories\User\UserRepository;
use App\Services\ImageAnalysis\CompareFaces;
use App\Services\Mail\MailgunService;
use App\UseCases\Admin\Manage\Users\UsersService;
use App\UseCases\Emails\Provider\ProfilePictureReminderService;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Testing\Fakes\NotificationFake;
use Tests\Builders\User\UserTrait;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use DatabaseTransactions;
    use UserTrait;

    /**
     * @var UsersService
     */
    private $service;
    /**
     * @var \Faker\Generator
     */
    private $faker;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
        $this->service = new UsersService(
            app(CompareFaces::class),
            app(SpecialistRepository::class),
            $this->userRepository,
            new Dispatcher(),
            app()->get(MailgunService::class),
            app()->get(ProfilePictureReminderService::class)
        );
        $this->faker = Factory::create();
    }

    public function testApproveProvider()
    {
        $user = $this->createProvider();
        $admin = $this->userRepository->getAdmin();
        $this->assertTrue($user->isWait());
        $this->service->approveProvider($user, $admin);
        $user = $this->userRepository->getById($user->id);
        $this->assertTrue($user->specialist->isApproved());
        $this->service->approveProvider($user, $admin);
        $user = $this->userRepository->getById($user->id);
        $this->assertTrue($user->specialist->isWaiting());
    }

    public function testDeleteUser()
    {
        $user = $this->createProvider();
        $this->service->deleteUser($user);
        $this->expectExceptionMessage('User not found');
        $this->userRepository->getById($user->id);
    }

    public function testUserToTest()
    {
        $user = $this->createProvider();
        $this->service->setUserToTest($user);
        $this->userRepository->getById($user->id);
        $this->assertTrue($user->isTestAccount());
    }
}
