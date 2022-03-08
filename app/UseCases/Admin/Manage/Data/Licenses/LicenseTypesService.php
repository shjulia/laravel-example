<?php

declare(strict_types=1);

namespace App\UseCases\Admin\Manage\Data\Licenses;

use App\Entities\Data\LicenseType;
use App\Entities\Data\LicenseTypePosition;
use App\Http\Requests\Admin\Data\LicenseType\CreateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class LicenseTypesService
 * Manage license types.
 *
 * @package App\UseCases\Admin\Manage\Data\Licenses
 */
class LicenseTypesService
{
    /**
     * @param CreateRequest $request
     * @return LicenseType
     */
    public function create(CreateRequest $request): LicenseType
    {
        DB::beginTransaction();
        try {
            $licenseType = LicenseType::create([
                'title' => $request->title
            ]);
            foreach ($request->position as $key => $position) {
                /** @var LicenseTypePosition $licenseTypePositions */
                $licenseTypePositions = $licenseType->licenseTypePositions()->create([
                    'position_id' => $position,
                    'required' => $request->required[$key] ?? 0
                ]);
                $licenseTypePositions->states()->attach($request->states[$key] ?? []);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \DomainException('Creating error');
        }
        return $licenseType;
    }

    /**
     * @param LicenseType $licenseType
     * @param CreateRequest $request
     */
    public function edit(LicenseType $licenseType, CreateRequest $request): void
    {
        DB::beginTransaction();
        try {
            $licenseType->update([
                'title' => $request->title
            ]);
            foreach ($request->position as $key => $position) {
                /** @var LicenseTypePosition $licenseTypePositions */
                $licenseTypePositions = $licenseType->licenseTypePositions()->updateOrCreate(
                    ['position_id' => $position],
                    ['required' => $request->required[$key] ?? 0]
                );
                $licenseTypePositions->states()->sync($request->states[$key] ?? []);
            }
            $licenseType->licenseTypePositions()
                ->whereNotIn('position_id', $request->position)
                ->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw new \DomainException('Updating error');
        }
    }

    /**
     * @param LicenseType $licenseType
     */
    public function destroy(LicenseType $licenseType): void
    {
        try {
            $licenseType->delete();
        } catch (\Exception $e) {
            throw new \DomainException('Deleting error');
        }
    }
}
