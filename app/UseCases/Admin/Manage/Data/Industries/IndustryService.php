<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data\Industries;

use App\Entities\Industry\Industry;
use App\Http\Requests\Admin\Data\Industry\CreateRequest;
use App\Http\Requests\Admin\Data\Industry\EditRequest;
use App\Repositories\Industry\IndustryRepository;

/**
 * Class IndustryService
 * Manage industries.
 *
 * @package App\UseCases\Admin\Manage\Data\Industries
 */
class IndustryService
{
    /**
     * @var IndustryRepository
     */
    private $industryRepository;

    /**
     * IndustryService constructor.
     * @param IndustryRepository $industryRepository
     */
    public function __construct(IndustryRepository $industryRepository)
    {
        $this->industryRepository = $industryRepository;
    }

    /**
     * @param CreateRequest $request
     * @return Industry
     */
    public function create(CreateRequest $request): Industry
    {
        try {
            $industry = Industry::createNew($request->title, $request->alias);
            $industry->saveOrFail();
        } catch (\Throwable $e) {
            throw new \DomainException('Creating error');
        }
        return $industry;
    }

    /**
     * @param Industry $industry
     * @param EditRequest $request
     * @return Industry
     */
    public function edit(Industry $industry, EditRequest $request): Industry
    {
        $industry =  $this->industryRepository->getById($industry->id);
        try {
            $industry->update([
                'title' => $request->title,
                'alias' => $request->alias
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('Updating error');
        }
        return $industry;
    }

    /**
     * @param Industry $industry
     */
    public function destroy(Industry $industry): void
    {
        $industry =  $this->industryRepository->getById($industry->id);
        try {
            $industry->delete();
        } catch (\Exception $e) {
            throw new \DomainException('Deleting error');
        }
    }
}
