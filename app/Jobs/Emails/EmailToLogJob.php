<?php

namespace App\Jobs\Emails;

use App\Entities\Notification\EmailLog;
use App\Entities\User\User;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Mailgun\Mailgun;

/**
 * Class EmailToLogJob
 * @package App\Jobs\Emails
 */
class EmailToLogJob implements ShouldQueue
{
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;
    use Queueable;

    /**
     * @var User
     */
    private $to;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $data;

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $class;

    /**
     * EmailToLogJob constructor.
     * @param User $to
     * @param string $subject
     * @param array $data
     * @param string $class
     */
    public function __construct(User $to, string $subject, array $data, string $class)
    {
        $this->to = $to;
        $this->subject = $subject;
        $this->data = json_encode($data);
        $this->date = Carbon::now()->subMinute(2)->format('r');//date("D, j M, Y H:i:s ") . "-0000";
        $this->class = $class;
    }

    public function handle(Mailgun $mailgun): void
    {
        $emailLog = EmailLog::create([
            'to' => $this->to->id,
            'subject' => $this->subject,
            'data' => $this->data,
            'class' => $this->class
        ]);
        sleep(10);
        $domain = config('services.mailgun.domain');

        $queryString = [
            'begin' => $this->date,
            'ascending' => 'yes',
            'limit' => 5,
            'pretty' => 'yes',
            'subject' => $emailLog->subject
        ];
        $result = $mailgun->get("$domain/events", $queryString);
        $lastLog = $result->http_response_body->items[count($result->http_response_body->items) - 1];
        $lastLog = json_decode(json_encode($lastLog), 1);
        $emailLog->update([
            'status' => $lastLog['event'],
            'message_id' => $lastLog['message']['headers']['message-id']
        ]);
    }
}
