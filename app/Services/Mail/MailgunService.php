<?php

declare(strict_types=1);

namespace App\Services\Mail;

use App\Entities\Notification\EmailLog;
use App\Entities\User\User;
use Illuminate\Http\Request;
use Mail;

/**
 * Class MailgunService
 * Works with Email Log. Updates email status and can resend message.
 *
 * @package App\Services\Mail
 */
class MailgunService
{
    /**
     * @param Request $request
     * @throws \Exception
     */
    public function updateStatus(Request $request)
    {
        $eventData = $request->get('event-data');
        $messageId = $eventData['message']['headers']['message-id'];
        $status = $request->get('event-data')['event'];

        if (!$emailLog = EmailLog::where('message_id', $messageId)->first()) {
            throw new \Exception('message ' . $messageId . ' not found');
        }
        $emailLog->update([
            'last_status' => $status,
        ]);
    }

    /**
     * resend message
     *
     * @param $id - message id
     */
    public function resendMessage(int $id)
    {
        $mail = EmailLog::find($id);
        $recipientEmail = User::find($mail->to)->email;

        Mail::html($mail->data, function ($message) use ($recipientEmail, $mail) {
            $message->to($recipientEmail);
            $message->subject($mail->subject);
        });
    }
}
