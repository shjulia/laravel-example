<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data\Positions;

use App\Entities\Data\LicenseTypePosition;
use App\Entities\Industry\Rate;
use App\Http\Requests\Admin\Data\Rate\CreateRequest;
use App\Http\Requests\Admin\Data\Rate\EditRequest;
use App\Repositories\Data\RateRepository;
use App\Repositories\Industry\PositionRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class RateService
 * Manage Rates.
 *
 * @package App\UseCases\Admin\Manage\Data\Positions
 */
class RateService
{
    /**
     * @var PositionRepository
     */
    private $positions;
    /**
     * @var RateRepository
     */
    private $rates;

    /**
     * RateService constructor.
     * @param PositionRepository $positions
     * @param RateRepository $rates
     */
    public function __construct(PositionRepository $positions, RateRepository $rates)
    {
        $this->positions = $positions;
        $this->rates = $rates;
    }

    /**
     * @param CreateRequest $request
     * @return Rate
     * @throws \Exception
     */
    public function create(CreateRequest $request): Rate
    {
        DB::beginTransaction();
        try {
            /** @var Rate $rate */
            $rate = Rate::create([
                'title' => $request->title
            ]);
            foreach ($request->position as $key => $position) {
                $pos = $this->positions->getById((int)$position);
                $rate->positions()->save($pos, $request->getPivotAttributes($key));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \DomainException('Creating error');
        }
        return $rate;
    }

    /**
     * @param Rate $rate
     * @param EditRequest $request
     * @throws \Exception
     */
    public function edit(Rate $rate, EditRequest $request): void
    {
        DB::beginTransaction();
        try {
            $rate->update([
                'title' => $request->title
            ]);
            $res = [];
            foreach ($request->position as $key => $position) {
                $res[(int)$position] = $request->getPivotAttributes($key);
            }
            $rate->positions()->sync($res);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \DomainException('Updating error');
        }
    }

    /**
     * @param Rate $rate
     */
    public function destroy(Rate $rate): void
    {
        try {
            $rate->delete();
        } catch (\Exception $e) {
            throw new \DomainException('Deleting error');
        }
    }
}
