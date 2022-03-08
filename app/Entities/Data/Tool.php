<?php

declare(strict_types=1);

namespace App\Entities\Data;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tool - Practice management software tools
 *
 * @package App\Entities\Data
 * @property int $id
 * @property string $title
 * @property int $is_regular
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Tool extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @param string $title
     * @return static
     */
    public static function createRegular(string $title): self
    {
        $tool = new self();
        $tool->title = $title;
        $tool->is_regular = true;
        return $tool;
    }
}
