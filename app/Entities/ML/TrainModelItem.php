<?php

declare(strict_types=1);

namespace App\Entities\ML;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TrainModelItem
 *
 * @package App\Entities\ML
 * @property int $id
 * @property string $image
 * @property string $items
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class TrainModelItem extends Model
{
    protected $table = 'train_model_items';
    protected $fillable = ['image', 'items'];
}
