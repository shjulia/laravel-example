<?php

declare(strict_types=1);

namespace App\Entities\Data;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Score - for quiz what provider|practice did well
 *
 * @package App\Entities\Data
 * @property int $id
 * @property string $title
 * @property string $for_type
 * @property int $active
 * @mixin \Eloquent
 */
class Score extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /** @var string  */
    public const PROVIDER_TYPE = 'provider';

    /** @var string  */
    public const PRACTICE_TYPE = 'practice';
}
