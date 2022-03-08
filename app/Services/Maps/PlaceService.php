<?php

declare(strict_types=1);

namespace App\Services\Maps;

use App\Helpers\S3Helper;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class PlaceService
 * Get place photo from Google Place services base on place name and address.
 *
 * API {@see https://developers.google.com/places/web-service/photos}
 * @package App\Services\Maps
 */
class PlaceService
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
    public const BASE_URL_PHOTO_REFERENCE = "https://maps.googleapis.com/maps/api/place/findplacefromtext/json";

    /** @var string  */
    public const BASE_URL_GET_PHOTO = "https://maps.googleapis.com/maps/api/place/photo";

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
     * @param string $name
     * @param string $country
     * @param string $city
     * @return null|string
     */
    public function getPlacePhoto(string $name, string $country, string $city): ?string
    {
        try {
            $photoReference = $this->getPlacePhotoReference($name . ', ' . $city . ', ' . $country);
            if (!$photoReference) {
                return null;
            }
            return $this->savePhotoToS3($photoReference);
        } catch (\Exception $e) {
        } catch (GuzzleException $ge) {
        }
        return null;
    }

    /**
     * @param string $query
     * @return null|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPlacePhotoReference(string $query): ?string
    {
        $params = [
            'input' => $query,
            'inputtype' => 'textquery',
            'fields' => 'name,photos',
            'key' => $this->key
        ];
        $url = self::BASE_URL_PHOTO_REFERENCE . '?' . http_build_query($params);
        $response = $this->client->request('GET', $url);
        $res = json_decode((string)$response->getBody());
        return $res->candidates[0]->photos[0]->photo_reference ?? null;
    }

    /**
     * @param string $photoReference
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function savePhotoToS3(string $photoReference): string
    {
        $params = [
            'maxwidth' => 400,
            'photoreference' => $photoReference,
            'key' => $this->key
        ];
        $url = self::BASE_URL_GET_PHOTO . '?' . http_build_query($params);
        $response = $this->client->request('GET', $url);
        return S3Helper::uploadImageByContent($response->getBody()->getContents());
    }
}
