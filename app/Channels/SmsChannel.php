<?php

declare(strict_types=1);

namespace App\Channels;

use App\Entities\Notification\EmailLog;
use App\Entities\User\User;
use App\Notifications\SmsNotification;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

/**
 * Class SmsChannel
 * @package App\Channels
 */
class SmsChannel
{
    /**
     * @var string
     */
    private $appId;

    /**
     * @var string
     */
    private $appSecret;

    /**
     * @var string
     */
    private $from;

    /**
     * SmsChannel constructor.
     * @param string $appId
     * @param string $appSecret
     * @param string $from
     */
    public function __construct(string $appId, string $appSecret, string $from)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->from = $from;
    }

    /**
     * @param User $notifiable
     * @param SmsNotification $notification
     */
    public function send($notifiable, SmsNotification $notification)
    {
        $to = $notifiable->phone;
        //if ($to != '+380985956322') { return;}
        $to = preg_replace('~[^0-9+]+~', '', $to);
        if (!$to) {
            return;
        }
        $to = $to{0} == '+' ? $to : '+1' . $to;
        $text = $notification->getData()->text . ' ' . $notification->getData()->link;
        try {
            $twilio = new Client($this->appId, $this->appSecret);
            $message = $twilio->messages
                ->create(
                    $to,
                    [
                        "body" => $text,
                        "from" => $this->from
                    ]
                );
        } catch (ConfigurationException $e) {
            \Log::error('Message: ' . $notification->getData()->text . ' To number: ' . $to . ' ' . $e->getMessage());
            return;
        } catch (TwilioException $e) {
            \Log::error('Message: ' . $notification->getData()->text . ' To number: ' . $to . ' ' . $e->getMessage());
            return;
        }
        if (!$message->sid) {
            \Log::error('Message: ' . $notification->getData()->text . ' To number: ' . $to . ' sending error');
            return;
        }
        $this->logSms($message->sid, $notifiable->id, $text, $to);
    }

    /**
     * @param string $messageId
     * @param int $userId
     * @param string $text
     * @param string $to
     */
    private function logSms(string $messageId, int $userId, string $text, string $to): void
    {
        EmailLog::create([
            'to' => $userId,
            'subject' => substr($text, 0, 200),
            'data' => $text,
            'last_status' => EmailLog::ACCEPTED,
            'message_id' => $messageId,
            'class' => 'sms',
            'contact' => $to
        ]);
    }
}
