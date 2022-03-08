<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Shift;

use App\Entities\Shift\Coupon;
use App\Http\Requests\Admin\Shift\Coupon\CreateRequest;
use App\Http\Requests\Admin\Shift\Coupon\EditRequest;
use Illuminate\Support\Facades\DB;

/**
 * Class CouponService
 * Manage Coupons.
 *
 * @package App\UseCases\Admin\Manage\Shift
 */
class CouponService
{
    /**
     * @param CreateRequest $request
     * @return Coupon
     * @throws \Exception
     */
    public function create(CreateRequest $request): Coupon
    {
        DB::beginTransaction();
        try {
            $coupon = Coupon::createBase(
                $request->code,
                \DateTimeImmutable::createFromFormat('Y-m-d', $request->start_date),
                \DateTimeImmutable::createFromFormat('Y-m-d', $request->end_date),
                $request->percent_off ? (float)$request->percent_off : null,
                $request->dollar_off ? (float)$request->dollar_off : null,
                $request->use_per_account_limit ? (float)$request->use_per_account_limit : null,
                $request->use_globally_limit ? (float)$request->use_globally_limit : null,
                $request->minimum_bill ? (float)$request->minimum_bill : null
            );
            $coupon->saveOrFail();
            if ($request->state) {
                $coupon->states()->sync($request->state);
            }
            if ($request->position) {
                $coupon->positions()->sync($request->position);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw new \DomainException('Coupon saving/ error');
        }


        return $coupon;
    }

    /**
     * @param EditRequest $request
     * @param Coupon $coupon
     * @throws \Exception
     */
    public function edit(EditRequest $request, Coupon $coupon): void
    {
        DB::beginTransaction();
        try {
            $coupon->editTime(
                \DateTimeImmutable::createFromFormat('Y-m-d', $request->start_date),
                \DateTimeImmutable::createFromFormat('Y-m-d', $request->end_date)
            );
            $coupon->edit(
                $request->percent_off ? (float)$request->percent_off : null,
                $request->dollar_off ? (float)$request->dollar_off : null,
                $request->use_per_account_limit ? (float)$request->use_per_account_limit : null,
                $request->use_globally_limit ? (float)$request->use_globally_limit : null,
                $request->minimum_bill ? (float)$request->minimum_bill : null
            );
            $coupon->saveOrFail();

            $coupon->states()->sync($request->state ?: []);
            $coupon->positions()->sync($request->position ?: []);
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw new \DomainException('Coupon updating error');
        }
    }

    /**
     * @param Coupon $coupon
     */
    public function delete(Coupon $coupon): void
    {
        try {
            $coupon->delete();
        } catch (\Exception $e) {
            throw new \DomainException('Deleting error');
        }
    }
}
