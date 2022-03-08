<?php

declare(strict_types=1);

namespace App\Services\Integration;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ConnectException;
use Psr\Log\LoggerInterface;

/**
 * Class CoreService
 * @package App\Services\Integration
 */
class CoreService
{
    /**
     * @var Client
     */
    private $client;
    /**
     * @var string
     */
    private $url;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var string
     */
    private $login;
    /**
     * @var string
     */
    private $password;

    public function __construct(Client $client, string $url, string $login, string $password, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->url = $url;
        $this->logger = $logger;
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @param string $method
     * @param string $url
     * @return array
     */
    public function fetch(string $method, string $url): array
    {
        try {
            $response = $this->client->request($method, $this->url . $url, $this->auth());
            $data = json_decode((string)$response->getBody(), true);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new \DomainException('Data fetching problems.');
        }
        return $data;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     */
    public function request(string $method, string $path, array $data): array
    {
        try {
            $response = $this->client->request($method, $this->url . $path, [
                'json' => $data,
                'auth' => $this->auth()['auth']
            ]);
            if (!in_array($response->getStatusCode(), [200, 201])) {
                throw new \DomainException('Something gone wrong');
            }
            $data = json_decode((string)$response->getBody(), true);
        } catch (ConnectException $e) {
            $this->logger->error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new \DomainException('Connection Exception');
        } catch (BadResponseException $e) {
            $this->logger->error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $response = json_decode((string)($e->getResponse() ? $e->getResponse()->getBody() : '[]'));
            throw new \DomainException($response->title ?? ($e->getResponse()->getReasonPhrase() ?? $e->getMessage()));
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            throw new \DomainException($e->getMessage());
        }
        return $data;
    }

    /**
     * @return array[]
     */
    private function auth(): array
    {
        return ['auth' => [$this->login, $this->password]];
    }
}
