<?php

declare(strict_types=1);

namespace App\Services\OneSignal;

use App\Entities\DTO\Notification as NotificationDTO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class CreateNotificationService
 * Sends push notifications using OneSignal service.
 *
 * API {@see https://documentation.onesignal.com/docs}
 * @package App\Services\OneSignal
 */
class CreateNotificationService
{
    public const CREATE_NOTIFICATION_URL = "https://onesignal.com/api/v1/notifications";

    /**
     * @param array $playerIds
     * @param NotificationDTO $notification
     */
    public function sendMessage(array $playerIds, NotificationDTO $notification)
    {
        $content = [
            "en" => $notification->title
        ];

        $fields = [
            'app_id' => config('services.onesignal.app_id'),
            'include_player_ids' => $playerIds,
            'contents' => $content,
        ];

        $client = new Client();
        try {
            $client->request('POST', self::CREATE_NOTIFICATION_URL, [
                'json' => $fields
            ]);
        } catch (GuzzleException $e) {
            \LogHelper::error($e);
        }
    }
}
