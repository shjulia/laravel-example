<?php

declare(strict_types=1);

namespace App\Services\Maps;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class AutocompletePlaceService
 * Autocompletes place names with Google Place Service to retrieve place data
 *
 * API {@see https://developers.google.com/places/web-service/autocomplete}
 * @package App\Services\Maps
 */
class AutocompletePlaceService
{
    /** @var string */
    public const BASE_URL_AUTOCOMPLETE = "https://maps.googleapis.com/maps/api/place/autocomplete/json";
    //"https://maps.googleapis.com/maps/api/place/findplacefromtext/json";

    /** @var string */
    public const BASE_URL_PLACE = "https://maps.googleapis.com/maps/api/place/details/json";

    /** @var array */
    public const PLACE_DATA = [
        'address' => '',
        'city' => '',
        'state' => '',
        'zip' => '',
        'url' => '',
        'phone' => ''
    ];

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
     * @param string $query
     * @param null|string $lat
     * @param null|string $lng
     * @param bool $establishment
     * @return array
     */
    public function getPlacesNamesByQuery(
        string $query,
        ?string $lat,
        ?string $lng,
        ?bool $establishment = false
    ): array {
        $params = [
            'input' => $query . ', USA',
            //'types' => 'dentist,doctor,hospital,pharmacy,spa,veterinary_care',
            //'fields' => 'name,place_id',
            //'inputtype' => 'textquery',
            'key' => $this->key,
            'language' => 'en'
        ];

        if ($establishment) {
            $params['types'] = 'establishment';
        }

        if ($lat && $lng) {
            $params['location'] = $lat . ',' . $lng;
            $params['radius'] = 10000; //25000;
        }
        try {
            $url = self::BASE_URL_AUTOCOMPLETE . '?' . http_build_query($params);
            $response = $this->client->request('GET', $url);
            $res = json_decode((string)$response->getBody());
            if ($res->status !== 'OK') {
                return [];
            }
            $data = [];
            foreach (/*$res->candidates*/ $res->predictions as $row) {
                //$data[$row->place_id] = $row->name;
                $data[$row->place_id] = $row->description;
            }
        } catch (\Exception $e) {
            \LogHelper::error($e);
            return [];
        } catch (GuzzleException $e) {
            \LogHelper::error($e);
            return [];
        }

        return $data;
    }

    /**
     * @param string $placeId
     * @return array
     */
    public function getPlaceData(string $placeId): array
    {
        $params = [
            'placeid' => $placeId,
            'fields' => 'address_components,formatted_address,name,formatted_phone_number,website',
            'key' => $this->key,
            'language' => 'en'
        ];
        try {
            $url = self::BASE_URL_PLACE . '?' . http_build_query($params);
            $response = $this->client->request('GET', $url);
            $res = json_decode((string)$response->getBody());

            if ($res->status !== 'OK') {
                return self::PLACE_DATA;
            }

            $res = $res->result;
            $data = self::PLACE_DATA;
            $data['address'] = $res->formatted_address ?? '';
            $data['url'] = $res->website ?? '';
            $data['phone'] = $res->formatted_phone_number ?? '';
            foreach ($res->address_components as $component) {
                $data[$component->types[0]] = $component->long_name ?? '';

                if ($component->types[0] == 'administrative_area_level_1') {
                    $data['state'] = $component->short_name;
                }
            }
            $data['zip'] = $data['postal_code'] ?? '';
            $data['city'] = $data['locality'] ?? '';
        } catch (\Exception $e) {
            \LogHelper::error($e);
            return self::PLACE_DATA;
        } catch (GuzzleException $e) {
            \LogHelper::error($e);
            return self::PLACE_DATA;
        }

        return $data;
    }
}
