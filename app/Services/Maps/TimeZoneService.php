<?php

declare(strict_types=1);

namespace App\Services\Maps;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class TimeZoneService
 * Defines timezone base on lat and lng using Google Maps Service.
 *
 * API {@see https://developers.google.com/maps/documentation/timezone/intro}
 * @package App\Services\Maps
 */
class TimeZoneService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $key;

    /** @var string  */
    public const BASE_URL = "https://maps.googleapis.com/maps/api/timezone/json";

    /**
     * GeocodeService constructor.
     * @param Client $client
     * @param string $key
     */
    public function __construct(Client $client, string $key)
    {
        $this->client = $client;
        $this->key = $key;
    }

    /**
     * @param string $lat
     * @param string $lng
     * @return string|null
     */
    public function getTimeZone(float $lat, float $lng): ?string
    {
        $params = [
            'location' => $lat . ',' . $lng,
            'timestamp' => time(),
            'key' => $this->key
        ];
        try {
            $url = self::BASE_URL . '?' . http_build_query($params);
            $response = $this->client->request('GET', $url);
            $response = json_decode((string)$response->getBody()->getContents());
            if ($response->status !== "OK") {
                return null;
            }
            $tz = $response->timeZoneId;
            if (!$tz) {
                return null;
            }
            return $tz;
        } catch (\Exception $e) {
            \LogHelper::error($e);
            return null;
        } catch (GuzzleException $e) {
            \LogHelper::error($e);
            return null;
        }
    }
}
