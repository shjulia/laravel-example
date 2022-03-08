<?php
namespace Tests\Integration\Services\Maps;

use App\Services\Maps\GeocodeService;
use Tests\TestCase;

class GeocodeTest extends TestCase
{
    /**
     * @var GeocodeService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(GeocodeService::class);
    }

    public function testGeocode()
    {
        $res = $this->service->getGeocode('800 Cherokee Ave SE, Atlanta, GA 30315, USA');
        $this->assertNotEmpty($res);
        $this->assertArrayHasKey('lat', $res);
        $this->assertArrayHasKey('lng', $res);
        $this->assertNotEmpty($res['lat']);
        $this->assertNotEmpty($res['lng']);
    }
}
