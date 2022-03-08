<?php
namespace Tests\Integration\Services\Maps;

use App\Services\Maps\PlaceService;
use Tests\TestCase;

/**
 * Class PlaceTest
 * @package Tests\Integration\Services\Maps
 */
class PlaceTest extends TestCase
{
    /**
     * @var PlaceService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(PlaceService::class);
    }

    public function testPlacePhoto()
    {
        $res = $this->service->getPlacePhotoReference("332 Ormond St SE 103, Atlanta, GA 30315, USA");
        $this->assertNotNull($res);
    }
}
