<?php

declare(strict_types=1);

namespace App\Services\Maps;

use App\Entities\DTO\Distance\Distance;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class DistanceService
 * Gets distances between two addresses using Google Dostance Matrix service.
 *
 * API {@see https://developers.google.com/maps/documentation/distance-matrix/start}
 * @package App\Services\Maps
 */
class DistanceService
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
    public const BASE_URL = "https://maps.googleapis.com/maps/api/distancematrix/json";

    /**
     * DistanceService constructor.
     * @param Client $client
     * @param string $key
     */
    public function __construct(Client $client, string $key)
    {
        $this->client = $client;
        $this->key = $key;
    }

    /**
     * @param string $address1
     * @param string $address2
     * @return Distance|null
     */
    public function getDistance(string $address1, string $address2): ?Distance
    {
        $params = [
            'origins' => urlencode($address1),
            'destinations' => urlencode($address2),
            'key' => $this->key
        ];
        try {
            $url = self::BASE_URL . '?' . http_build_query($params);
            $response = $this->client->request('GET', $url);
            $response = json_decode((string)$response->getBody());
            $distance = $response->rows[0]->elements[0] ?? null;
            if (!$distance) {
                return null;
            }
            return new Distance(
                $distance->distance->value,
                $distance->distance->text,
                $distance->duration->value,
                $distance->duration->text
            );
        } catch (\Exception $e) {
            return null;
        } catch (GuzzleException $e) {
            return null;
        }
    }

    /**
     * @param string $address1
     * @param array $addresses
     * @return array|Distance[]
     */
    public function getDistances(string $address1, array $addresses): ?array
    {
        $params = [
            'origins' => urlencode($address1),
            'destinations' => str_replace(" ", "+", implode('|', $addresses)),
            'key' => $this->key
        ];
        try {
            $url = self::BASE_URL . '?' . http_build_query($params);
            $response = $this->client->request('GET', $url);
            $response = json_decode((string)$response->getBody());
            $distances = [];
            for ($i = 0; $i < count($addresses); $i++) {
                $distance = $response->rows[0]->elements[$i] ?? null;
                if (!$distance) {
                    $distances[] = null;
                    continue;
                }
                $distances[] = new Distance(
                    $distance->distance->value,
                    $distance->distance->text,
                    $distance->duration->value,
                    $distance->duration->text
                );
            }
            return $distances;
        } catch (\Exception $e) {
            return null;
        } catch (GuzzleException $e) {
            return null;
        }
    }
}
