<?php

declare(strict_types=1);

namespace App\Repositories\Shift\Coupons;

use App\Entities\Shift\Coupon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class CouponRepository
 * @package App\Repositories\Shift\Coupons
 */
class CouponRepository
{
    /**
     * @return Coupon[]|Collection
     */
    public function findAllCustom()
    {
        return Coupon::whereNull('practice_id')
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->get();
    }

    /**
     * @return Coupon[]|Collection
     */
    public function findAllAuto()
    {
        return Coupon::whereNotNull('practice_id')
            ->where('end_date', '>=', now()->format('Y-m-d'))
            ->get();
    }

    /**
     * @param string|null $code
     * @return Coupon
     */
    public function getByCode(?string $code = null): Coupon
    {
        if (!$code) {
            throw new \DomainException('Coupon code must be set.');
        }
        $coupon = Coupon::where('code', $code)
            ->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))
            ->first();
        if (!$coupon) {
            throw new \DomainException('Coupon code not found.');
        }
        return $coupon;
    }
}
