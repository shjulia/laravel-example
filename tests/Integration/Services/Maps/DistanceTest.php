<?php
namespace Tests\Integration\Services\Maps;

use App\Entities\DTO\Distance\Distance;
use App\Services\Maps\DistanceService;
use Tests\TestCase;

class DistanceTest extends TestCase
{
    /**
     * @var DistanceService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(DistanceService::class);
    }

    public function testDistanceDetection()
    {
        $this->assertTrue(true);
        /** @var Distance $res */
        $distance = $this->service->getDistance(
            '800 Cherokee Ave SE, Atlanta, GA 30315, USA',
            '332 Ormond St SE #103, Atlanta, GA 30315, USA'
        );
        $this->assertNotNull($distance);
        $this->assertIsFloat($distance->distanceVal);
        $this->assertIsFloat($distance->durationVal);
        $this->assertIsString($distance->distanceText);
        $this->assertIsString($distance->durationText);
    }
}
