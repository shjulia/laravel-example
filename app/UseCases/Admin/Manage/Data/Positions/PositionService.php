<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data\Positions;

use App\Entities\Industry\Position;
use App\Http\Requests\Admin\Data\Position\CreateRequest;
use App\Http\Requests\Admin\Data\Position\EditRequest;
use App\Repositories\Industry\IndustryRepository;
use App\Repositories\Industry\PositionRepository;

/**
 * Class PositionService
 * Manage positions.
 *
 * @package App\UseCases\Admin\Manage\Data\Positions
 */
class PositionService
{
    /**
     * @var PositionRepository
     */
    private $positionRepository;
    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * PositionService constructor.
     * @param PositionRepository $positionRepository
     */
    public function __construct(
        PositionRepository $positionRepository,
        IndustryRepository $industryRepository
    ) {
        $this->positionRepository = $positionRepository;
        $this->industryRepository = $industryRepository;
    }

    /**
     * @param CreateRequest $request
     * @return Position
     */
    public function create(CreateRequest $request): Position
    {
        $industry = $this->industryRepository->getById($request->industry);
        try {
            $position = Position::createNew(
                $request->title,
                $industry,
                (float)$request->fee,
                (float)$request->minimum_profit,
                (float)$request->surge_price,
                (int)$request->parent_id
            );
            $position->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Creating error');
        }
        return $position;
    }

    /**
     * @param Position $position
     * @param EditRequest $request
     * @return Position
     */
    public function edit(Position $position, EditRequest $request): Position
    {
        $position = $this->positionRepository->getById($position->id);
        try {
            $position->update([
                'title' => $request->title,
                'industry_id' => $request->industry,
                'fee' => $request->fee,
                'minimum_profit' => $request->minimum_profit,
                'parent_id' => $request->parent_id,
                'surge_price' => $request->surge_price
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('Updating error');
        }
        return $position;
    }

    /**
     * @param Position $position
     */
    public function destroy(Position $position): void
    {
        $position = $this->positionRepository->getById($position->id);
        try {
            $position->delete();
        } catch (\Exception $e) {
            throw new \DomainException('Deleting error');
        }
    }
}
