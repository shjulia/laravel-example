<?php

namespace Tests\Integration\Services\ImageAnalysis;

use App\Services\ImageAnalysis\DriverLicenseAnalysisAI;
use Tests\TestCase;

/**
 * Class DriverLicenseTest
 * @package Tests\Integration\Services\ImageAnalysis
 */
class DriverLicenseTest extends TestCase
{
    /**
     * @var DriverLicenseAnalysisAI
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(DriverLicenseAnalysisAI::class);
    }

    public function testDriverAnalysis(): void
    {
        $data = $this->service->analyzeImage('https://boonb.s3.amazonaws.com/dev/AOXFgtK4gQsBRcQjDNfMXi9iXzL53PFkT3asF75Z.jpeg');
        $this->assertNotNull($data['license']);
        $this->assertNotNull($data['address']);
        $this->assertNotNull($data['city']);
        $this->assertNotNull($data['state']);
        $this->assertNotNull($data['zip']);
    }
}
