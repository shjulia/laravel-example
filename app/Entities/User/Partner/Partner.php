<?php

declare(strict_types=1);

namespace App\Entities\User\Partner;

use App\Entities\User\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Partner - User type who registered only for referral program.
 *
 * @package App\Entities\User\Partner
 * @property int $user_id
 * @property string $description
 * @property string $description_answer
 * @property-read \App\Entities\User\User $user
 * @mixin \Eloquent
 */
class Partner extends Model
{
    /**
     * Has relation one to one with User
     *
     * @var string
     */
    protected $primaryKey = "user_id";

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * @return array
     */
    public static function descriptionsList(): array
    {
        return [
            'provider' =>  ['title' => 'Dental Practitioner'],
            'practice' => ['title' => 'Practice Administrator'],
            'leader' => [
                'title' => 'Key Opinion Leader',
                'data' => [
                    'Henry Schein', 'Benco', 'Patterson', 'Burkhart', 'Dentsply', 'Other'
                ]
            ],
            'sales' => ['title' => 'Sales Rep'],
            'other' => ['title' => 'Other']
        ];
    }
}
