<?php

namespace Tests\Integration\Services\Payment;

use App\Entities\User\FundingSource;
use App\Repositories\User\UserRepository;
use App\Services\Payment\DwollaService;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Builders\User\UserTrait;
use Tests\TestCase;

class DwollaTest extends TestCase
{
    use UserTrait;
    use DatabaseTransactions;

    /**
     * @var DwollaService
     */
    private $service;
    /**
     * @var \App\Entities\User\User
     */
    private $user;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->userRepository = app(UserRepository::class);
        $this->faker = Factory::create();
    }

    public function setUp()
    {
        parent::setUp();
        $this->service = app(DwollaService::class);
        $this->user = $this->createProvider();
    }

    public function testBankDetailsSaving()
    {
        $routingNumber = "222222226";
        $accountNumber = "123456789";
        $this->service->saveBankDetails([
            'routing_number' => $routingNumber,
            'account_number' => $accountNumber,
            'user' => $this->user
        ]);
        $user = $this->userRepository->getById($this->user->id);
        $customerId = $user->dwolla_customer_id;
        $this->assertNotNull($customerId);
        /** @var FundingSource $fundingSource */
        $fundingSource = $user->bankDetails();
        $this->assertNotNull($fundingSource);
        $this->assertEquals($routingNumber, $fundingSource->routing_number);
        $this->assertEquals($accountNumber, $fundingSource->account_number);
        $this->assertNotNull($fundingSource->funding_source_id);
    }

    public function testBankDetailsSavingTwice()
    {
        $routingNumber = "222222226";
        $accountNumber = "123456789";
        $this->service->saveBankDetails([
            'routing_number' => $routingNumber,
            'account_number' => $accountNumber,
            'user' => $this->user
        ]);
        $user = $this->userRepository->getById($this->user->id);
        $customerId = $user->dwolla_customer_id;

        $this->service->saveBankDetails([
            'routing_number' => $routingNumber,
            'account_number' => $accountNumber,
            'user' => $user
        ]);
        $user = $this->userRepository->getById($this->user->id);
        $this->assertEquals($customerId, $user->dwolla_customer_id);
        /** @var FundingSource $fundingSource */
        $fundingSource = $user->bankDetails();
        $this->assertNotNull($fundingSource);
        $this->assertEquals(1, count($user->fundingSources));
        $this->assertEquals($routingNumber, $fundingSource->routing_number);
        $this->assertEquals($accountNumber, $fundingSource->account_number);
        $this->assertNotNull($fundingSource->funding_source_id);
    }
}
