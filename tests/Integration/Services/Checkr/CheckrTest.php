<?php

namespace Tests\Unit\Services\Checkr;

use App\Entities\DTO\Provider\DataForCheck;
use App\Services\Checkr\CheckrService;
use Faker\Factory;
use Tests\TestCase;

class CheckrTest extends TestCase
{
    /**
     * @var \Faker\Generator
     */
    private $faker;
    /**
     * @var CheckrService
     */
    private $service;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Factory::create();
    }

    public function setUp()
    {
        parent::setUp();
        $this->service = app(CheckrService::class);
    }

    public function testClient()
    {
        $data = new DataForCheck($this->faker->firstName,
            $this->faker->firstName,
            $this->faker->lastName,
            $this->faker->date,
            '111-11-2001',
            $email = mb_strtolower(str_replace(' ', '_', $this->faker->firstName)) . '@gmail.com',
            $this->faker->phoneNumber,
            '48071',
            'F1112001',
            'CA'
        );
        $candidate = $this->service->createCandidate($data);
        $this->assertNotEmpty($candidate);
        $this->assertArrayHasKey('success', $candidate);
        $this->assertNotNull($candidate['success']['id']);
        $this->assertEquals($email, $candidate['success']['email']);
        return $candidate['success']['id'];
    }

    /**
     * @depends testClient
     * @param string $id
     * @return string
     */
    public function testReport(string $id)
    {
        $report = $this->service->createReport($id);
        $this->assertNotEmpty($report);
        $this->assertArrayHasKey('success', $report);
        $this->assertNotNull($report['success']['id']);
        $this->assertEquals($id, $report['success']['candidate_id']);
        $this->assertEquals("pending", $report['success']['status']);
        return $report['success']['id'];
    }

    /**
     * @depends testReport
     * @param string $id
     */
    public function testReportGet(string $id)
    {
        $report = $this->service->getReport($id);
        $this->assertNotEmpty($report);
        $this->assertArrayHasKey('success', $report);
        $this->assertNotNull($report['success']['id']);
    }
}
