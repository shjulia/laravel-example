<?php

namespace Tests\Integration\Services\ImageAnalysis;

use App\Services\ImageAnalysis\CompareFaces;
use Tests\TestCase;

/**
 * Class CompareFacesTest
 * @package Tests\Integration\Services\ImageAnalysis
 */
class CompareFacesTest extends TestCase
{
    /**
     * @var CompareFaces
     */
    private $service;

    public function setUp()
    {
        parent::setUp();
        $this->service = app(CompareFaces::class);
    }

    public function testSameFaces()
    {
        $this->assertTrue(true);
        $res = $this->service->analyzeImage(
            "dev/AOXFgtK4gQsBRcQjDNfMXi9iXzL53PFkT3asF75Z.jpeg",
            "dev/1licpers.png"
        );
        $this->assertNotNull($res);
        $this->assertTrue($res > 50);
    }

    public function testDifferentFaces()
    {
        $res = $this->service->analyzeImage(
            "dev/AOXFgtK4gQsBRcQjDNfMXi9iXzL53PFkT3asF75Z.jpeg",
            "dev/0y3ChIBTFik8b4imCxLm4uL5AwtVikn6Wb0ZJd6j.jpg"
        );
        $this->assertNotNull($res);
        $this->assertTrue($res < 50);
    }
}
