<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data\Specialities;

use App\Entities\Industry\Speciality;
use App\Http\Requests\Admin\Data\Speciality\CreateRequest;
use App\Http\Requests\Admin\Data\Speciality\EditRequest;
use App\Repositories\Industry\SpecialityRepository;

/**
 * Class SpecialityService
 * Manage specialities.
 *
 * @package App\UseCases\Admin\Manage\Data\Specialities
 */
class SpecialityService
{
    /**
     * @var SpecialityRepository
     */
    private $specialityRepository;

    /**
     * SpecialityService constructor.
     * @param SpecialityRepository $specialityRepository
     */
    public function __construct(SpecialityRepository $specialityRepository)
    {
        $this->specialityRepository = $specialityRepository;
    }

    /**
     * @param CreateRequest $request
     * @return Speciality
     */
    public function create(CreateRequest $request): Speciality
    {
        try {
            $speciality = Speciality::create([
                'title' => $request->title,
                'industry_id' => $request->industry
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('Creating error');
        }
        return $speciality;
    }

    /**
     * @param Speciality $speciality
     * @param EditRequest $request
     * @return Speciality
     */
    public function edit(Speciality $speciality, EditRequest $request): Speciality
    {
        $speciality = $this->specialityRepository->getById($speciality->id);
        try {
            $speciality->update([
                'title' => $request->title,
                'industry_id' => $request->industry
            ]);
        } catch (\Exception $e) {
            throw new \DomainException('Updating error');
        }
        return $speciality;
    }

    /**
     * @param Speciality $speciality
     */
    public function destroy(Speciality $speciality): void
    {
        $speciality = $this->specialityRepository->getById($speciality->id);
        try {
            $speciality->delete();
        } catch (\Exception $e) {
            throw new \DomainException('Deleting error');
        }
    }
}
