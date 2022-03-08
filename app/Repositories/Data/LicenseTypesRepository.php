<?php

namespace App\Repositories\Data;

use App\Entities\Data\LicenseType;

/**
 * Class LicenseTypesRepository
 * @package App\Repositories\Data
 */
class LicenseTypesRepository
{
    /**
     * @return LicenseType[]
     */
    public function findAll()
    {
        return LicenseType::with('positions')->get();
    }

    /**
     * @param int $position
     * @param string $state
     * @return array
     */
    public function findByPositionAndState(int $position, string $state)
    {
        $types = LicenseType::whereHas('licenseTypePositions', function ($query) use ($position, $state) {
                $query->where('position_id', $position)
                ->whereHas('states', function ($query) use ($state) {
                    $query->where('short_title', $state);
                });
        })
            ->with(['licenseTypePositions' => function ($query) use ($position, $state) {
                $query->where('position_id', $position)
                    ->whereHas('states', function ($query) use ($state) {
                        $query->where('short_title', $state);
                    });
            }])
            ->get();
        $requiredLicense = [];
        $anotherLicense = [];
        foreach ($types as $type) {
            foreach ($type->licenseTypePositions as $licenseTypePosition) {
                if ($licenseTypePosition->required) {
                    $requiredLicense[] = [
                        'id' => $type->id,
                        'title' => $type->title,
                        'required' => 1
                    ];
                    continue;
                }
                $anotherLicense[] = [
                    'id' => $type->id,
                    'title' => $type->title,
                    'required' => 0
                ];
            }
        }
        return [
            'requiredLicense' => $requiredLicense,
            'anotherLicense' => $anotherLicense,
        ];
    }

    /**
     * @param int $id
     * @return LicenseType
     */
    public function getById(int $id)
    {
        $licenseType = LicenseType::where('id', $id)
            ->with(['licenseTypePositions' => function ($query) {
                $query->with(['states', 'position']);
            }])
            ->first();
        if (!$licenseType) {
            throw new \DomainException('License type not found');
        }
        return $licenseType;
    }

    /**
     * @param LicenseType $licenseType
     * @return array
     */
    public function licenseTypeArray(LicenseType $licenseType): array
    {
        $data['title'] = $licenseType->title;
        $i = 1;
        foreach ($licenseType->licenseTypePositions as $licenseTypePositions) {
            $data['position'][$i] = $licenseTypePositions->position_id;
            $data['required'][$i] = $licenseTypePositions->required;
            $data['states'][$i] = $licenseTypePositions->states->pluck('id')->toArray();
            $i++;
        }
        return $data;
    }
}
