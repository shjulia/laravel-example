<?php

use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Repositories\User\PracticeRepository;
use App\Repositories\User\SpecialistRepository;
use Illuminate\Database\Seeder;

/**
 * Class AddAreaToUsers
 */
class AddAreaToUsers extends Seeder
{
    /**
     * @var SpecialistRepository
     */
    private $specialistRepository;

    /**
     * @var PracticeRepository
     */
    private $practiceRepository;

    /**
     * AddAreaToUsers constructor.
     * @param SpecialistRepository $specialistRepository
     * @param PracticeRepository $practiceRepository
     */
    public function __construct(SpecialistRepository $specialistRepository, PracticeRepository $practiceRepository)
    {
        $this->specialistRepository = $specialistRepository;
        $this->practiceRepository = $practiceRepository;
    }

    /**
     * @throws Exception
     */
    public function run(): void
    {
        /** @var Specialist[] $specialists */
        $specialists = Specialist::where('driver_address', '!=', null)->get();
        foreach ($specialists as $specialist) {
            $this->specialistRepository->setArea(
                $specialist,
                $specialist->driver_state,
                $specialist->driver_city,
                $specialist->driver_zip
            );
        }

        /** @var Practice[] $practices */
        $practices = Practice::where('address', '!=', null)
            ->get();
        foreach ($practices as $practice) {
            $this->practiceRepository->setArea(
                $practice,
                $practice->state,
                $practice->city,
                $practice->zip
            );
        }
    }
}
