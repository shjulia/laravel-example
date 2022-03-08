<?php

declare(strict_types=1);

namespace App\Entities\User;

use App\Entities\Invite\Invite;
use App\Entities\Notification\EmailLog;
use App\Entities\Notification\EmailMark;
use App\Entities\User\Partner\Partner;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\Wallet\Wallet;
use App\Mail\Password\ResetPasswordEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use NotificationChannels\WebPush\HasPushSubscriptions;
use Ramsey\Uuid\Uuid;

/**
 * Class User - base class for User entity
 *
 * @package App\Entities\User
 * @property int $id
 * @property string $uuid
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $first_name
 * @property string $last_name
 * @property string|null $phone
 * @property string|null $tmp_token token for signup
 * @property string $status
 * @property string|null $signup_step signup step to know where user should be redirected
 * @property string|null $tz user time zone
 * @property string|null $dwolla_customer_id
 * @property int $is_test_account
 * @property int|null $signup_reminder_counter
 * @property string|null $last_signup_action_date
 * @property \Illuminate\Support\Carbon|null $last_remind_action_time
 * @property int $is_rejected
 * @property-read Collection|\App\Entities\User\ApproveLog[] $approveLogs log for all account actions
 * @property-read Collection|\Laravel\Passport\Client[] $clients
 * @property-read Collection|\App\Entities\Notification\EmailLog[] $emailLogs log for all emails sent to user
 * @property-read Collection|\App\Entities\Notification\EmailMark[] $emailMarks log for sent remind notifications
 * @property-read string $full_name
 * @property-read Practice|null $practice practice object for practice user type
 * @property null|string $tmp_password
 * @property-read Collection|\App\Entities\User\Practice\Practice[] $practices
 * @property-read \App\Entities\User\Referral $referral object with referral data
 * @property-read \App\Entities\User\Provider\Specialist $specialist provider object for provider user type
 * @property-read Wallet $wallet
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User\User active() AR builder for active users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Entities\User\User rejected() AR builder for rejected users
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasPushSubscriptions;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'tmp_token'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'last_remind_action_time'  => 'datetime',
    ];

    /** @var string */
    public const ACTIVE = 'active';
    /** @var string */
    public const WAITING = 'waiting';

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $step
     * @return static
     */
    public static function createBySignUp(
        string $email,
        string $firstName,
        string $lastName,
        string $step
    ): self {
        $user = new self();
        $user->uuid = Uuid::uuid4()->toString();
        $user->email = $email;
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->signup_step = $step;
        $user->setWaitingStatus();
        $user->setTmpToken();
        $user->setRandomPassword();
        return $user;
    }

    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $step
     * @param string|null $phone
     */
    public function editByNewType(string $firstName, string $lastName, string $step, ?string $phone = null): void
    {
        $this->first_name = $firstName;
        $this->last_name = $lastName;
        $this->phone = $phone;
        $this->signup_step = $step;
        $this->setWaitingStatus();
        $this->setTmpToken();
    }

    public function createWallet(string $id): void
    {
        if ($this->wallet) {
            throw new \DomainException('Wallet is already exists');
        }
        $this->wallet = Wallet::createBase($this, $id);
    }

    public function markWalletAsPaymentDataSet(): void
    {
        if (!$this->wallet) {
            throw new \DomainException('Wallet is not already exists');
        }
        $this->wallet->markAsPaymentDataSet();
    }

    public function markWalletAsTransferDataSet(): void
    {
        if (!$this->wallet) {
            throw new \DomainException('Wallet is not already exists');
        }
        $this->wallet->markAsTransferDataSet();
    }

    /**
     * @return void
     */
    public function setWaitingStatus(): void
    {
        $this->status = self::WAITING;
    }

    /**
     * @return void
     */
    public function setActiveStatus(): void
    {
        $this->status = self::ACTIVE;
    }

    /**
     * @return bool
     */
    public function isWait()
    {
        return $this->status === self::WAITING;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->status === self::ACTIVE;
    }

    /**
     * @return void
     */
    public function setTmpToken(): void
    {
        $this->tmp_token = strtolower(str_random(20));
    }

    /**
     * Check if user has already finished sighup process
     *
     * @return bool
     */
    public function isSignupFinished(): bool
    {
        return !($this->tmp_token && $this->signup_step);
    }

    /**
     * @return void
     */
    public function setRandomPassword(): void
    {
        $pass = str_random(8);
        $this->password = bcrypt($pass);
        $this->tmp_password = $pass;
    }

    /**
     * @param string $tmpPassword
     */
    public function setTmpPasswordAttribute(string $tmpPassword)
    {
        $this->tmp_password = $tmpPassword;
    }

    /**
     * @return null|string
     */
    public function getTmpPasswordAttribute()
    {
        return $this->tmp_password;
    }

    /**
     * 1 to 1 with Provider relation
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function specialist()
    {
        return $this->hasOne(Specialist::class);
    }

    /**
     * 1 to 1 with Provider relation alias
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function provider()
    {
        return $this->hasOne(Specialist::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * 1 to 1 for referral
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function referral()
    {
        return $this->hasOne(Referral::class);
    }

    /**
     * Partner user type (for referral program)
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function partner()
    {
        return $this->hasOne(Partner::class);
    }

    /**
     * Practices
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function practices()
    {
        return $this->belongsToMany(Practice::class, 'user_practice')
            ->withPivot('is_creator', 'practice_role');
    }

    /**
     * Relation to webpush players
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Funding sources (Dwolla wallets)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fundingSources()
    {
        return $this->hasMany(FundingSource::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function passwordSetup()
    {
        return $this->hasOne(PasswordSetup::class, 'user_id', 'id');
    }

    /**
     * Last wallet data
     * @return FundingSource|null
     */
    public function bankDetails(): ?FundingSource
    {
        $fs = $this->fundingSources;
        return $fs[0] ?? null;
    }

    /**
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * @return Practice|null
     */
    public function getPracticeAttribute(): ?Practice
    {
        return $this->practices()->first();
    }

    /**
     * One user can have several different roles
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return (bool)$this->roles->contains('type', Role::ROLE_SUPER_ADMIN);
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return (bool)$this->roles->contains('type', Role::ROLE_ADMIN);
    }

    /**
     * @return bool
     */
    public function isAdminStatuses(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    /**
     * @return bool
     */
    public function isProvider(): bool
    {
        return (bool)$this->roles->contains('type', Role::ROLE_PROVIDER);
    }

    /**
     * @return bool
     */
    public function isPractice(): bool
    {
        return (bool)$this->roles->contains('type', Role::ROLE_PRACTICE);
    }

    /**
     * @return bool
     */
    public function isPartner(): bool
    {
        return (bool)$this->roles->contains('type', Role::ROLE_PARTNER);
    }

    /**
     * @return bool
     */
    public function isCustomerSuccess(): bool
    {
        return (bool)$this->roles->contains('type', Role::ROLE_CUSTOMER_SUCCESS);
    }

    /**
     * @return bool
     */
    public function isAccountant(): bool
    {
        return (bool)$this->roles->contains('type', Role::ROLE_ACCOUNTANT);
    }

    /**
     * @return array
     */
    public static function statusesList(): array
    {
        return [
            self::WAITING => 'Waiting approval',
            self::ACTIVE => 'Active'
        ];
    }

    /**
     * sent reset password email
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $email = $this->getEmailForPasswordReset();
        Mail::to($email)->send(new ResetPasswordEmail($token));
    }

    /**
     * @return bool
     */
    public function isTestAccount(): bool
    {
        return (bool)$this->is_test_account;
    }

    /**
     * @return void
     */
    public function setTestAccount(): void
    {
        $this->is_test_account = !$this->is_test_account;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class, 'to', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function approveLogs()
    {
        return $this->hasMany(ApproveLog::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailMarks()
    {
        return $this->hasMany(EmailMark::class, 'user_id', 'id');
    }

    /**
     * @return bool
     */
    public function isRejected(): bool
    {
        return (bool)$this->is_rejected;
    }

    public function reject(): void
    {
        if ($this->isRejected()) {
            throw new \DomainException('User have been already rejected');
        }
        $this->is_rejected = 1;
    }

    public function unReject(): void
    {
        if (!$this->isRejected()) {
            throw new \DomainException('User have been already un-rejected');
        }
        $this->is_rejected = 0;
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('is_rejected', 0);
    }

    public function scopeRejected(Builder $query)
    {
        return $query->where('is_rejected', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invite()
    {
        return $this->hasOne(Invite::class, 'user_id', 'id');
    }

    public function scopeDeactivated(Builder $query)
    {
        return $query->where('is_active', 0);
    }

    public function deactivate(): void
    {
        $this->is_active = false;
    }

    public function activate(): void
    {
        $this->is_active = true;
    }

    public function isAccountActive(): bool
    {
        return (bool)$this->is_active;
    }

    /**
     * @return \Illuminate\Support\Carbon|null
     */
    public function initialSignupCompletedTime()
    {
        $finished = null;
        /** @var ApproveLog[] $record */
        foreach ($this->approveLogs as $record) {
            if (!$record->isFinishedStep()) {
                continue;
            }
            $finished = $record->created_at;
            break;
        }
        return $finished;
    }

    /**
     * @return \Illuminate\Support\Carbon|null
     */
    public function approvedTime()
    {
        $finished = null;
        /** @var ApproveLog[] $record */
        foreach ($this->approveLogs as $record) {
            if (!$record->isApproved()) {
                continue;
            }
            $finished = $record->created_at;
            break;
        }
        return $finished;
    }
}

/**
 * @SWG\Definition(
 *     definition="UserBase",
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="email", type="string"),
 *     @SWG\Property(property="phone", type="string"),
 *     @SWG\Property(property="first_name", type="string"),
 *     @SWG\Property(property="last_name", type="string"),
 *     @SWG\Property(property="status", type="string"),
 *     @SWG\Property(property="signup_step", type="string")
 * )
 */

/**
 * @SWG\Definition(
 *     definition="UserPractice",
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="email", type="string"),
 *     @SWG\Property(property="phone", type="string"),
 *     @SWG\Property(property="first_name", type="string"),
 *     @SWG\Property(property="last_name", type="string"),
 *     @SWG\Property(property="status", type="string"),
 *     @SWG\Property(property="signup_step", type="string"),
 *     @SWG\Property(property="practice", type="object", ref="#/definitions/Practice")
 * )
 */

/**
 * @SWG\Definition(
 *     definition="UserProvider",
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="email", type="string"),
 *     @SWG\Property(property="phone", type="string"),
 *     @SWG\Property(property="first_name", type="string"),
 *     @SWG\Property(property="last_name", type="string"),
 *     @SWG\Property(property="status", type="string"),
 *     @SWG\Property(property="signup_step", type="string"),
 *     @SWG\Property(property="specialist", type="object", ref="#/definitions/Provider")
 * )
 */
