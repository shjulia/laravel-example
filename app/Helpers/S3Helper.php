<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class S3Helper - Helper to work with Amazon S3
 * @package App\Helpers
 */
class S3Helper
{
    /**
     * @param $file
     * @return false|string
     */
    public static function uploadImage($file)
    {
        $awsPath = env('AWS_PATH');
        return Storage::disk('s3')->putFile($awsPath, $file, 'public');
    }

    /**
     * @param $content
     * @return string
     */
    public static function uploadImageByContent($content): string
    {
        $awsPath = env('AWS_PATH');
        $path = $awsPath . '/' . Str::random(40) . '.jpg';
        Storage::disk('s3')->put($path, $content, 'public');
        return $path;
    }

    /**
     * @param $content
     * @return string
     */
    public static function uploadBase64($content): string
    {
        $pos = strpos($content, ',');
        $content = substr($content, $pos + 1);
        return S3Helper::uploadImageByContent(base64_decode($content));
    }

    /**
     * @param string $path
     * @return string
     */
    public static function getUrlByPath(string $path)
    {
        return Storage::disk('s3')->url($path);
    }

    /**
     * @param string $path
     * @return bool
     */
    public static function deleteFile(string $path)
    {
        return Storage::disk('s3')->delete($path);
    }
}
