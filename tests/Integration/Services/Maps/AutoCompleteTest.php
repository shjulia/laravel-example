<?php
namespace Tests\Integration\Services\Maps;

use App\Services\Maps\AutocompletePlaceService;
use Tests\TestCase;

class AutoCompleteTest extends TestCase
{
    /**
     * @var AutocompletePlaceService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(AutocompletePlaceService::class);
    }

    public function testBaseSearch()
    {
        $query = 'dental atlanta';
        $res = $this->service->getPlacesNamesByQuery($query, null, null);
        $this->assertNotEmpty($res);
        foreach ($res as $key => $val) {
            foreach (explode(' ', $query) as $word) {
                $this->assertTrue(strripos($val, $word) !== false);
            }
        }
        reset($res);
        return key($res);
    }

    /**
     * @depends testBaseSearch
     * @param string $placeId
     */
    public function testPlaceData(string $placeId)
    {
        $placeData = $this->service->getPlaceData($placeId);
        $this->assertNotEmpty($placeData);
        $this->assertArrayHasKey('address', $placeData);
        $this->assertArrayHasKey('url', $placeData);
        $this->assertArrayHasKey('phone', $placeData);
        $this->assertArrayHasKey('zip', $placeData);
        $this->assertArrayHasKey('city', $placeData);
        $this->assertNotEmpty($placeData['address']);
    }

    public function testSearchWithParams()
    {
        $query = 'dental atlanta';
        $res = $this->service->getPlacesNamesByQuery($query, 47.8492488, 35.131176, true);
        $this->assertNotEmpty($res);
    }
}
