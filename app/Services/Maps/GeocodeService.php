<?php

declare(strict_types=1);

namespace App\Services\Maps;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class GeocodeService
 * Gets address' lat and lng using Google Geocode service.
 *
 * API {@see https://developers.google.com/maps/documentation/geocoding/start}
 * @package App\Services\Maps
 */
class GeocodeService
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
    public const BASE_URL = "https://maps.googleapis.com/maps/api/geocode/json";

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
     * @param string $address
     * @return array
     */
    public function getGeocode(string $address): array
    {
        $params = [
            'address' => urlencode($address),
            'key' => $this->key
        ];
        try {
            $url = self::BASE_URL . '?' . http_build_query($params);
            $response = $this->client->request('GET', $url);
            $response = json_decode((string)$response->getBody());
            if ($response->status !== "OK") {
                return ['lat' => '', 'lng' => ''];
            }
            $geocode = $response->results[0]->geometry->location ?? null;
            if (!$geocode) {
                return ['lat' => '', 'lng' => ''];
            }
            return [
                'lat' => $geocode->lat,
                'lng' => $geocode->lng
            ];
        } catch (\Exception $e) {
            \LogHelper::error($e);
            return ['lat' => '', 'lng' => ''];
        } catch (GuzzleException $e) {
            \LogHelper::error($e);
            return ['lat' => '', 'lng' => ''];
        }
    }
}
