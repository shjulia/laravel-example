<?php

declare(strict_types=1);

namespace App\Events\Mail;

use App\Entities\Notification\EmailLog;
use App\Entities\User\User;
use Illuminate\Mail\Events\MessageSent;

/**
 * Class MessageIdTranscriptEvent
 * Saves email info into Email Log.
 *
 * @package App\Events\Mail
 */
class MessageIdTranscriptEvent
{
    /**
    * Handle the event.
    *
    * @param  MessageSent  $event
    * @return void
    */
    public function handle(MessageSent $event)
    {
        try {
            $email = $event->message->getHeaders()->get('to')->getFieldBody('mailboxes');
            $data = $event->message->getBody();
            $messageId = $event->message->getId();
            $user = User::where('email', $email)->first();
            if (!$user) {
                return;
            }
            $subject = $event->message->getHeaders()->get('subject')->getFieldBody('value');

            EmailLog::create([
                'to' => $user->id,
                'subject' => $subject,
                'data' => $data,
                'last_status' => EmailLog::ACCEPTED,
                'message_id' => $messageId,
                'class' => 'email',
                'contact' => $user->email
            ]);
        } catch (\Exception $e) {
            \LogHelper::error($e);
        }
    }
}
