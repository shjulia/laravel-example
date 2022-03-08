<?php

declare(strict_types=1);

namespace App\Services\ImageAnalysis;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * @deprecated
 * Class DriverLicenseAnalysisAI
 * Sends request to our AI server to analyze driver's license photo to convert license data to text.
 *
 * @package App\Services\ImageAnalysis
 */
class DriverLicenseAnalysisAI
{
    /**
     * @var array
     */
    private $data = [
        'first_name' => '',
        'last_name' => '',
        'middle_name' => '',
        'expiration_date' => '',
        'dob' => '',
        'address' => '',
        'city' => '',
        'state' => null,
        'zip' => '',
        'gender' => null,
        'license' => ''
    ];

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $url;

    /**
     * DriverLicenseAnalysisAI constructor.
     * @param Client $client
     * @param string $url
     * @param string $apiKey
     */
    public function __construct(Client $client, string $url, ?string $apiKey = '')
    {
        $this->apiKey = $apiKey;
        $this->client = $client;
        $this->url = $url;
    }

    /**
     * @param string $path
     * @return array
     */
    public function analyzeImage(string $path)
    {
        try {
            $response = $this->client->request('GET', $this->url . '?' . 'image=' . $path);
            $data = json_decode((string)$response->getBody());
            return  $this->parse($data);
        } catch (\Exception $e) {
            \LogHelper::error($e);
        } catch (GuzzleException $e) {
            \LogHelper::error($e);
        }

        return $this->data;
    }

    /**
     * @param array $resData
     * @return array
     */
    private function parse(array $resData): array
    {
        if (!isset($resData[0])) {
            return $this->data;
        }
        $res = $resData[0];
        $this->data['first_name'] = $res->person->first_name ?? '';
        $this->data['last_name'] = $res->person->last_name ?? '';
        $this->data['middle_name'] = $res->person->second_name ?? '';
        $this->data['expiration_date'] = Carbon::parse($res->dates->exp)->format('Y-m-d') ?? '';
        $this->data['dob'] = Carbon::parse($res->dates->dob)->format('Y-m-d') ?? '';
        $this->data['address'] = $res->person->address[0]->street ?? '';
        $this->data['city'] = $res->person->address[0]->area->city ?? '';
        $this->data['state'] = $res->person->address[0]->area->state ?? null;
        $this->data['zip'] = $res->person->address[0]->area->zip ?? '';
        $this->data['gender'] = $res->person->gender ?? null;
        $this->data['license'] = $res->dln ?? '';
        return $this->data;
    }
}
