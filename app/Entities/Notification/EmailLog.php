<?php

declare(strict_types=1);

namespace App\Entities\Notification;

use App\Entities\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Class EmailLog - All emails and text notifications sent to user
 *
 * @package App\Entities\Notification
 * @property int $id
 * @property int $to
 * @property string $class
 * @property string $subject
 * @property string $data
 * @property string|null $last_status
 * @property string|null $message_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $contact
 * @property-read User $user
 * @mixin \Eloquent
 */
class EmailLog extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     *  Mailgun accepted the request to send/forward the email and the message has been placed in queue.
     */
    public const ACCEPTED = 'accepted';

    /**
     * Mailgun rejected the request to send/forward the email.
     */
    public const REJECTED = 'rejected';

    /**
     *  Mailgun sent the email and it was accepted by the recipient email server.
     */
    public const DELIVERED = 'delivered';

    /**
     * Mailgun could not deliver the email to the recipient email server.
     */
    public const FAILED = 'failed';

    /**
     * The email recipient opened the email and enabled image viewing. Open tracking must be enabled in the Mailgun
     * control panel, and the CNAME record must be pointing to mailgun.org.
     */
    public const OPENED = 'opened';

    /**
     * Subjects list of marketing emails
     */
    public const MARKETING_SUBJECTS = [
        'We Miss You! Here\'s a Small Gift',
        'It\'s time to hire your first Provider',
        'We Will Meet You in the Middle. Here\'s 50% off Your Next Temp',
        'Request Temps Confidently via Boon',
        'You\'re Missing Out',
        'You\'re on your way to earning extra cash!',
        'You have worked more than 40 hours via Boon!',
        'You\'re a Road Warrior',
        'Get $100, Tell a Friend about Boon',
        'You\'re Missing Out on $100+'
    ];

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return in_array($this->status, [self::FAILED, self::OPENED, self::REJECTED]);
    }

    /**
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'to');
    }

    /**
     * @return bool
     */
    public function isSms(): bool
    {
        return $this->class === "sms";
    }

    /**
     * @return bool
     */
    public function isEmail(): bool
    {
        return $this->class === "email";
    }
}
