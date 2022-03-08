<?php

declare(strict_types=1);

namespace App\Entities\User\Provider;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Checkr - provider check result
 *
 * @package App\Entities\User\Provider
 * @mixin \Eloquent
 * @property int $id
 * @property int $specialist_id
 * @property string $checkr_status
 * @property string|null $checkr_candidate_id
 * @property string|null $checkr_report_id
 * @property string|null $checkr_error_response
 * @property string|null $checkr_success_response
 * @property int $checkr_attempts
 */
class Checkr extends Model
{
    /** @inheritdoc */
    protected $guarded = [];

    /** @inheritdoc */
    public $timestamps = false;

    /** @var string  */
    public const CHECKR_NOT_SET = "not set";

    /** @var string  */
    public const CHECKR_PENDING = "pending";

    /** @var string  */
    public const CHECKR_CLEAR = "clear";

    /** @var string  */
    public const CHECKR_CONSIDER = "consider";

    /**
     * @return bool
     */
    public function isClear(): bool
    {
        return $this->checkr_status === self::CHECKR_CLEAR;
    }

    /**
     * @return bool
     */
    public function isConsider(): bool
    {
        return $this->checkr_status === self::CHECKR_CONSIDER;
    }

    /**
     * @return bool
     */
    public function isNotSet(): bool
    {
        return $this->checkr_status === self::CHECKR_NOT_SET;
    }
}
