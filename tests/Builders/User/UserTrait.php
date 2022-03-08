<?php

namespace Tests\Builders\User;

use App\Entities\User\User;
use App\Fake\Event\Dispatcher;
use App\Fake\Services\Driver\DriverLicense\Photo\FakeAddService;
use App\Fake\Services\Driver\FakeCreateService;
use App\Fake\Services\Driver\FakeSSNService;
use App\Fake\Services\Wallet\Provider\FakeWalletService;
use App\Http\Requests\Auth\Provider\CheckRequest;
use App\Http\Requests\Auth\Provider\IndustryRequest;
use App\Http\Requests\Auth\Provider\UserBaseRequest;
use App\Repositories\Industry\PositionRepository;
use App\Repositories\User\SpecialistRepository;
use App\UseCases\Auth\Provider\RegisterService;
use App\UseCases\Auth\UserCreatorService;

trait UserTrait
{
    private function createProvider(): User
    {
        $registerService = new RegisterService(
            app(UserCreatorService::class),
            app(SpecialistRepository::class),
            new Dispatcher(),
            app(FakeCreateService::class),
            app(FakeAddService::class),
            app(FakeSSNService::class),
            app(FakeWalletService::class)
        );
        $data = new UserBaseRequest();
        $data->merge([
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
        ]);
        $user = $registerService->userBaseSave($data);
        $positions = app()->get(PositionRepository::class);
        $position = $positions->getByTitle('Dentist');
        $data = new IndustryRequest();
        $data->merge([
            'industry' => $position->industry_id,
            'position' => $position->id
        ]);
        $registerService->industrySave($data, $user);
        $data = new CheckRequest();
        $ssn = '111112001';
        $data->merge([
            'ssn' => $ssn
        ]);
        $registerService->checkSave($data, $user);
        return $this->userRepository->getById($user->id);
    }
}
