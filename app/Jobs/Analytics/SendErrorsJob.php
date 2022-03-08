<?php

namespace App\Jobs\Analytics;

use App\Mail\Admin\ErrorMail;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

/**
 * Class SendErrorsJob
 * @package App\Jobs\Analytics
 */
class SendErrorsJob
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int|null
     */
    private $userId;

    /**
     * @var int|null
     */
    private $code;

    /**
     * @var string|null
     */
    private $file;

    /**
     * @var int|null
     */
    private $line;

    /**
     * @var string|null
     */
    private $trace;

    /**
     * @var array
     */
    private $emails = [];

    /**
     * SendErrorsJob constructor.
     * @param string $message
     * @param int|null $userId
     * @param int|null $code
     * @param string|null $file
     * @param int|null $line
     * @param string|null $trace
     */
    public function __construct(string $message, ?int $userId, ?int $code, ?string $file, ?int $line, ?string $trace)
    {
        $this->message = $message;
        $this->userId = $userId;
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
        $this->trace = $trace;
        if (config('app.developer_email')) {
            $this->emails[] = config('app.developer_email');
        }
        if (config('app.manager_email')) {
            $this->emails[] = config('app.manager_email');
        }
    }

    /**
     *
     */
    public function handle(): void
    {
        $content = "<h3>Doing boon app error</h3>
        <p><b>Error message </b>$this->message</p>
        <p><b>User </b>$this->userId</p>
        <p><b>Code </b>$this->code</p>
        <p><b>File </b>$this->file</p>
        <p><b>Line </b>$this->line</p>
        <p><b>Trace </b>$this->trace</p>
        ";
        Mail::to($this->emails)->send(new ErrorMail($content));
    }
}
