<?php

declare(strict_types=1);

namespace App\Http\Validation;

use GuzzleHttp\Client;

/**
 * Class ReCaptcha
 * @package App\Http\Validation
 */
class ReCaptcha
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $key;

    /**
     * ReCaptcha constructor.
     * @param Client $client
     * @param string $key
     */
    public function __construct(Client $client, string $key)
    {
        $this->client = $client;
        $this->key = $key;
    }

    public function validate($attribute, $value, $parameters, $validator)
    {
        $response = $this->client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' =>
                    [
                        'secret' => $this->key,
                        'response' => $value
                    ]
            ]
        );
        $body = json_decode((string)$response->getBody());
        return $body->success;
    }
}
