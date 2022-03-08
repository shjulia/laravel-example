<?php

declare(strict_types=1);

namespace App\Entities\NewsLetter;

use App\Entities\User\Role;
use Illuminate\Database\Eloquent\Model;
use Webmozart\Assert\Assert;

/**
 * Class NewsLetter
 * @property int $id
 * @property int template_id
 * @property string $start_date
 * @property string $subject
 * @property string $tz
 * @property bool $is_finished
 * @property string $emails
 * @property int|null $role_id
 */
class NewsLetter extends Model
{
    protected $guarded = [];

    protected $table = 'newsletter_newsletters';

    protected $casts = ['emails' => 'array'];

    public static function create(
        Template $template,
        string $subject,
        \DateTimeImmutable $date,
        string $tz,
        ?Role $role
    ): self {
        $newsLetter = new self();
        $newsLetter->template_id = $template->id;
        $newsLetter->subject = $subject;
        $newsLetter->start_date = $date->format('Y-m-d H:i');
        $newsLetter->tz = $tz;
        $newsLetter->is_finished = false;
        $newsLetter->emails = json_encode([]);
        $newsLetter->role_id = $role ? $role->id : null;
        return $newsLetter;
    }

    public function edit(Template $template, string $subject, \DateTimeImmutable $date, string $tz, ?Role $role): void
    {
        if ($this->isStarted()) {
            throw new \DomainException('NewsLetter has been already started. You can\'t edit it.');
        }
        $this->template_id = $template->id;
        $this->subject = $subject;
        $this->start_date = $date->format('Y-m-d H:i');
        $this->tz = $tz;
        $this->role_id = $role ? $role->id : null;
    }

    public function attachEmail(string $email): void
    {
        $emails = json_decode($this->emails, true);
        foreach ($emails as $e) {
            if ($email === $e || false === \filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return;
            }
        }
        $emails[] = $email;
        $this->emails = json_encode($emails);
    }

    public function finish(): void
    {
        if ($this->is_finished) {
            throw new \DomainException('Sending is already ');
        }
        $this->is_finished = true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function template()
    {
        return $this->hasOne(Template::class, 'id', 'template_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }

    public function startedInMinutes(): float
    {
        $now = (new \DateTimeImmutable())->getTimestamp();
        $start = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $this->start_date);
        return round(($this->getUTCTime($start)->getTimestamp() - $now) / 60, 2);
    }

    public function isStarted(): bool
    {
        return $this->startedInMinutes() <= 0;
    }

    private function getUTCTime(\DateTimeImmutable $dateTime): \DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            $dateTime->format('Y-m-d H:i'),
            new \DateTimeZone($this->tz)
        );
        Assert::notFalse($date);
        return $date->setTimezone(new \DateTimeZone('UTC'));
    }
}
