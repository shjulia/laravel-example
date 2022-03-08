<?php

declare(strict_types=1);

namespace App\Entities\Industry;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Industry - for practices and providers union
 *
 * @package App\Entities\Industry
 * @property int $id
 * @property string $title
 * @property string $alias
 * @property-read Collection|\App\Entities\Industry\Position[] $positions
 * @property-read Collection|\App\Entities\Industry\Speciality[] $specialities
 * @mixin \Eloquent
 */
class Industry extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param string $title
     * @param string $alias
     * @return static
     */
    public static function createNew(string $title, string $alias): self
    {
        $industry = new self();
        $industry->title = $title;
        $industry->alias = $alias;
        return $industry;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function specialities()
    {
        return $this->hasMany(Speciality::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function positions()
    {
        return $this->hasMany(Position::class);
    }
}
