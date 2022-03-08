<?php

declare(strict_types=1);

namespace App\Services\ImageAnalysis;

use Aws\Rekognition\RekognitionClient;

/**
 * Class CompareFaces
 * Compare faces from provider's profile picture and driver license.
 *
 * @package App\Services\ImageAnalysis
 */
class CompareFaces
{
    /**
     * @var RekognitionClient
     */
    private $client;

    /**
     * @var string
     */
    private $bucket;

    /**
     * DriverLicenseAnalysis constructor.
     * @param RekognitionClient $client
     * @param string $bucket
     */
    public function __construct(RekognitionClient $client, string $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
    }

    /**
     * @param string $path1
     * @param string $path2
     * @return float
     */
    public function analyzeImage(string $path1, string $path2)
    {
        $result = $this->client->compareFaces([
            'SimilarityThreshold' => 1,
            'SourceImage' => [
                'S3Object' => [
                    'Bucket' => $this->bucket,
                    'Name' => $path1
                ],
            ],
            'TargetImage' => [
                'S3Object' => [
                    'Bucket' => $this->bucket,
                    'Name' => $path2
                ],
            ]
        ]);
        $data = $this->parseResult($result->toArray());

        return $data;
    }

    /**
     * @param array $result
     * @return float
     */
    private function parseResult(array $result): float
    {
        return $result['FaceMatches'][0]['Similarity'];
    }
}
