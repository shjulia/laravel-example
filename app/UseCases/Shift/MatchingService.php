<?php

declare(strict_types=1);

namespace App\UseCases\Shift;

use App\Entities\Shift\Shift;
use App\Entities\Statistics\MatchingSteps;
use App\Entities\User\Practice\AddressDTO;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Exceptions\Shift\NoProvidersAreAvailableException;
use App\Repositories\Data\DistanceRepository;
use App\Repositories\Shift\Matching\MatchingRepository;
use App\Repositories\Shift\ShiftRepository;
use App\Repositories\Statistics\MatchingStepsRepository;
use App\Services\Maps\DistanceService;

/**
 * Class MatchingService
 * Finds suitable providers for a shift.
 *
 * @package App\UseCases\Shift
 */
class MatchingService
{
    /**
     * @var DistanceService
     */
    private $distanceService;

    /**
     * @var DistanceRepository
     */
    private $distanceRepository;

    /**
     * @var MatchingStepsRepository
     */
    private $matchingStepsRepository;

    /**
     * @var MatchingRepository
     */
    private $matchingRepository;

    /**
     * @var CostService
     */
    private $costService;

    /**
     * @var int
     */
    private $try = 1;

    /**
     * @var Shift
     */
    private $shift;

    /**
     * @var ShiftRepository
     */
    private $shiftRepository;

    /** @var int */
    public const LIMIT_BEFORE_DISTANCE = 300;

    /** @var int  */
    public const DISTANCE_LIMIT_1 = 30 * 60; // 30 min

    /** @var int  */
    public const DISTANCE_LIMIT_2 = 60 * 60; // 1h

    /**
     * MatchingService constructor.
     * @param DistanceService $distanceService
     * @param DistanceRepository $distanceRepository
     * @param MatchingStepsRepository $matchingStepsRepository
     * @param ShiftRepository $shiftRepository
     * @param MatchingRepository $matchingRepository
     * @param CostService $costService
     */
    public function __construct(
        DistanceService $distanceService,
        DistanceRepository $distanceRepository,
        MatchingStepsRepository $matchingStepsRepository,
        ShiftRepository $shiftRepository,
        MatchingRepository $matchingRepository,
        CostService $costService
    ) {
        $this->distanceService = $distanceService;
        $this->distanceRepository = $distanceRepository;
        $this->matchingStepsRepository = $matchingStepsRepository;
        $this->shiftRepository = $shiftRepository;
        $this->matchingRepository = $matchingRepository;
        $this->costService = $costService;
    }

    /**
     * @param Shift $shift
     * @return array
     * @throws NoProvidersAreAvailableException
     */
    public function match(Shift $shift): array
    {
        $this->shift = $shift;
        $this->try = $this->matchingStepsRepository->findNexStep($shift->id);
        /** @var Practice $practice */
        $practice = $shift->practice;

        /*if ($this->try == 1) {
            event(new NotifyNotAvailableProviders($shift));
        }*/

        $providers = $this->getBaseFoundProviders($shift->position_id, $shift->practice_location->state);
        if (empty($providers)) {
            throw new NoProvidersAreAvailableException();
        }
        $providers = $this->getByRates($providers);
        if (empty($providers)) {
            throw new NoProvidersAreAvailableException();
        }
        $providers = $this->getByHired($providers);
        if (empty($providers)) {
            throw new NoProvidersAreAvailableException();
        }
        $providers = $this->getByHolidays($providers);
        if (empty($providers)) {
            throw new NoProvidersAreAvailableException();
        }
        $providers = $this->getByAvailabilities($providers);
        if (empty($providers)) {
            throw new NoProvidersAreAvailableException();
        }
        try {
            $providersNew = $this->getByReviews($providers, $practice->id);
            if ($res = $this->check($providers, $providersNew)) {
                return $this->getProvider($res);
            }

            $providersNew = $this->getByAverage($providers, $practice->average_stars_to_provider);
            if ($res = $this->check($providers, $providersNew)) {
                return $this->getProvider($res);
            }
            $providersNew = $this->getByArea($providers, $practice);
            if ($res = $this->check($providers, $providersNew)) {
                return $this->getProvider($res);
            }

            $providersNew = $this->getBySetProvidersDistance($providers);
            if ($res = $this->check($providers, $providersNew)) {
                return $this->getProvider($res);
            }

            $providersNew = $this->getByDistance($providers, $practice);
            if (empty($providersNew)) {
                throw new NoProvidersAreAvailableException();
            }
            if ($res = $this->check($providers, $providersNew)) {
                return $this->getProvider($res);
            }

            $providersNew = $this->getByLicenceState($providers, $practice->state);
            if ($res = $this->check($providers, $providersNew)) {
                return $this->getProvider($res);
            }

            $providersNew = $this->getByTasks($providers, $shift);
            if ($res = $this->check($providers, $providersNew)) {
                return $this->getProvider($res);
            }

            $result = $this->finalMatching($providers, $practice);
        } catch (NoProvidersAreAvailableException $e) {
            throw new NoProvidersAreAvailableException();
        } catch (\Exception $e) {
            \LogHelper::error($e, ['userId' => $shift->creator->id]);
            throw new \DomainException('Search problem detected. Please try again.');
        }

        return $result;
    }

    /**
     * For testing
     * @param Shift $shift
     */
    public function setData(Shift $shift): void
    {
        $this->shift = $shift;
        $this->try = $this->matchingStepsRepository->findNexStep($shift->id);
    }

    /**
     * @param Shift $shift
     * @return array
     */
    public function getNotAvailabelProviders(Shift $shift): array
    {
        $this->shift = $shift;
        /** @var Practice $practice */
        $practice = $shift->practice;

        $providersNA = $this->getBaseFoundNotAvailabelProviders($shift->position_id, $practice->state);
        $providersNA = array_merge($providersNA, $this->getNotAvailableByAvailabilitySettings($shift));
        $providersNew = $this->getByDistance($providersNA, $practice, false);
        return $providersNew;
    }

    /**
     * @param Shift $shift
     * @return array
     */
    private function getNotAvailableByAvailabilitySettings(Shift $shift): array
    {
        $providers = $this->getBaseFoundProviders($shift->position_id, $shift->practice->state, false);
        if (empty($providers)) {
            return [];
        }
        $providers = $this->getByHired($providers, false);
        if (empty($providers)) {
            return [];
        }
        $providersA = $this->getByAvailabilities($providers, false);
        return array_diff($providers, $providersA);
    }

    /**
     * @param array $providers
     * @param array $providersNew
     * @return int|null
     */
    private function check(array &$providers, array &$providersNew): ?int
    {
        if (empty($providersNew)) {
            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::EMPTY_RESULT,
                []
            );
            unset($providersNew);
        } elseif (count($providersNew) === 1) {
            reset($providersNew);
            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::ONE_RESULT,
                [$providersNew[0]]
            );
            return $providersNew[0];
        } else {
            $providers = $providersNew;
            unset($providersNew);
        }
        return null;
    }

    /**
     * @param int $providerId
     * @return array
     */
    private function getProvider(int $providerId): array
    {
        $specialist = Specialist::where('user_id', $providerId)
            ->with(['user', 'position', 'specialities'])
            ->first()
            ->setAppends(['photo_url']);
        $distanceVal = $this->getDistanceVal($specialist, $this->shift->practice);
        $distance = $this->distanceRepository->findDistance(
            $specialist->user_id,
            $this->shift->practice_id,
            $this->shift->practice_location->addressId,
            true
        );
        return array_merge($specialist->toArray(), [
            'distance' => $distance,
            'distanceVal' => $distanceVal
        ]);
    }

    /**
     * @param array $providers
     * @param Practice $practice
     * @return array
     */
    private function finalMatching(array $providers, Practice $practice): array
    {
        if ($practice->hires_title == 0) {
            $provider = Specialist::whereIn('user_id', $providers)
                ->orderBy('average_stars', 'DESC')
                ->orderBy('hours_total', 'DECS')
                ->first();

            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::NO_HIRES_PRACTICE,
                [$provider->user_id]
            );
            return $this->getProvider($provider->user_id);
        }

        $provider = Specialist::whereIn('user_id', $providers);
        if ($practice->average_stars_to_providers > 4 && $practice->hires_total > 10) {
            $provider = $provider->orderBy('hours_total', 'ASC')->first();
            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::FIRST_JOB,
                [$provider->user_id]
            );
            return $this->getProvider($provider->user_id);
        }
        if ($tool = $practice->tool_id) {
            $providerWT = clone $provider;
            $providerWT = $providerWT->where('tool_id', $tool)->first();
            if ($providerWT) {
                $this->matchingStepsRepository->createStep(
                    $this->shift->id,
                    $this->try,
                    MatchingSteps::BY_MANAGEMENT_TOOLS,
                    [$providerWT->user_id]
                );
                return $this->getProvider($providerWT->user_id);
            }
        }
        $provider = $provider->inRandomOrder()->first();
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::FINAL_MATCHING,
            [$provider->user_id]
        );
        return $this->getProvider($provider->user_id);
    }

    /**
     * @param array $providers
     * @return array
     */
    private function getByRates(array $providers): array
    {
        $hourRate = $this->shift->cost / ($this->shift->shift_time / 60);
        $providers = $this->matchingRepository->getByRate($providers, $hourRate);
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::BY_RATES,
            $providers
        );
        return $providers;
    }

    /**
     * @param array $providers
     * @return array
     */
    private function getByHolidays(array $providers): array
    {
        $providers = $this->matchingRepository->getByHolidays($providers, $this->shift);
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::BY_HOLIDAY,
            $providers
        );
        return $providers;
    }

    /**
     * @param array $providers
     * @param string $state
     * @return array
     */
    private function getByLicenceState(array $providers, string $state): array
    {
        $providers = $this->matchingRepository->getByLicenceState($providers, $state);
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::LICENSE_STATE,
            $providers
        );

        return $providers;
    }

    /**
     * @param Specialist $provider
     * @param Practice $practice
     * @return float
     */
    private function getDistanceVal(Specialist $provider, Practice $practice): float
    {
        /** @var AddressDTO $location */
        $location = $this->shift->practice_location;

        if (
            $distanceVal = $this->distanceRepository->findDistance(
                $provider->user_id,
                $practice->id,
                $location->addressId
            )
        ) {
            return $distanceVal;
        }
        $distance = $this->distanceService->getDistance($location->fullAddress(), $provider->full_address);
        if ($distance) {
            $this->distanceRepository->createDistance(
                $provider->user_id,
                $practice->id,
                $location->addressId,
                $distance
            );
            return $distance->durationVal;
        }
        return 9999999.9;
    }

    /**
     * @param array $providers
     * @return array
     */
    public function getBySetProvidersDistance(array $providers): array
    {
        $providers = $this->costService->getByDistance($providers, $this->shift);
        if (!empty($providers)) {
            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::BY_PROVIDERS_DISTANCE,
                $providers
            );
            return $providers;
        }
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::NO_IN_PROVIDERS_DISTANCE,
            []
        );
        return [];
    }

    /**
     * @param array $providers
     * @param Practice $practice
     * @param bool|null $createStep
     * @return array
     */
    private function getByDistance(array $providers, Practice $practice, ?bool $createStep = true): array
    {
        $providersNew30m = [];
        $providersNew1h = [];
        foreach (Specialist::whereIn('user_id', $providers)->get() as $provider) {
            $distanceVal = $this->getDistanceVal($provider, $practice);

            if ($distanceVal <= self::DISTANCE_LIMIT_1) { //30min
                $providersNew30m[] = $provider->user_id;
            } elseif ($distanceVal <= self::DISTANCE_LIMIT_2) { //1h
                $providersNew1h[] = $provider->user_id;
            }
        }
        if (!empty($providersNew30m)) {
            if ($createStep) {
                $this->matchingStepsRepository->createStep(
                    $this->shift->id,
                    $this->try,
                    MatchingSteps::BY_30M,
                    $providersNew30m
                );
            }
            return $providersNew30m;
        }
        if (!empty($providersNew1h)) {
            if ($createStep) {
                $this->matchingStepsRepository->createStep(
                    $this->shift->id,
                    $this->try,
                    MatchingSteps::BY_1H,
                    $providersNew30m
                );
            }
            return $providersNew1h;
        }
        if ($createStep) {
            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::NO_IN_DISTANCE,
                []
            );
        }
        return $this->getClosestByDistance($providers, $createStep);
    }

    /**
     * @param array $providers
     * @param bool|null $createStep
     * @return array
     */
    private function getClosestByDistance(array $providers, ?bool $createStep = true): array
    {
        $providers = ($this->distanceRepository->findForShift($this->shift, $providers))
            ->pluck('provider_id')->toArray();
        if (empty($providers)) {
            if ($createStep) {
                $this->matchingStepsRepository->createStep(
                    $this->shift->id,
                    $this->try,
                    MatchingSteps::CLOSEST_NOT_FOUND,
                    []
                );
            }
            return [];
        }
        if ($createStep) {
            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::CLOSEST_PROVIDERS,
                $providers
            );
        }
        return $providers;
    }

    /**
     * @param array $providers
     * @param Practice $practice
     * @return array
     */
    private function getByArea(array $providers, Practice $practice): array
    {
        //if (!$practice->area_id) {
            return $this->getByZip($providers, $practice);
        //}
        $providersNew = $this->matchingRepository->getByArea($providers, $practice);
        $count = count($providersNew);
        if ($count > self::LIMIT_BEFORE_DISTANCE) {
            $providersNew = array_rand($providersNew, self::LIMIT_BEFORE_DISTANCE);
        }
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::BY_AREA,
            $providersNew
        );
        if (empty($providersNew)) {
            return $this->getByZip($providers, $practice);
        }
        return $providersNew;
    }

    /**
     * @param array $providers
     * @param Practice $practice
     * @return array
     */
    private function getByZip(array $providers, Practice $practice): array
    {
        $providersNew = $this->matchingRepository->getByZip($providers, $this->shift->practice_location->zip);
        $count = count($providersNew);
        if ($count > self::LIMIT_BEFORE_DISTANCE) {
            $providersNew = array_rand($providersNew, self::LIMIT_BEFORE_DISTANCE);
        }
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::BY_ZIP,
            $providersNew
        );
        if (empty($providersNew)) {
            return $this->getByCity($providers, $practice);
        }
        return $providersNew;
    }

    /**
     * @param array $providers
     * @param Practice $practice
     * @return array
     */
    private function getByCity(array $providers, Practice $practice): array
    {
        $providersNew = $this->matchingRepository->getByCity($providers, $this->shift->practice_location->city);
        $count = count($providersNew);
        if ($count > self::LIMIT_BEFORE_DISTANCE) {
            $providersNew = array_rand($providersNew, self::LIMIT_BEFORE_DISTANCE);
        }
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::BY_CITY,
            $providersNew
        );
        return $providersNew;
    }

    /**
     * @param int $positionId
     * @param string $state
     * @param bool|null $createStep
     * @return array
     */
    private function getBaseFoundProviders(int $positionId, string $state, ?bool $createStep = true): array
    {
        $excludes = $this->shiftRepository->getExcludedProviders($this->shift);
        $isTest = (bool)($this->shift->creator->is_test_account ?? false);
        $providers = $this->matchingRepository->getBaseFoundProviders($positionId, $state, $excludes, $isTest);

        if ($createStep) {
            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::BASE,
                $providers
            );
        }

        return $providers;
    }

    /**
     * @param int $positionId
     * @param string $state
     * @return array
     */
    private function getBaseFoundNotAvailabelProviders(int $positionId, string $state): array
    {
        $excludes = $this->shiftRepository->getExcludedProviders($this->shift);
        return $this->matchingRepository->getBaseFoundNotAvailabelProviders($positionId, $state, $excludes);
    }

    /**
     * @param array $providers
     * @param bool|null $createStep
     * @return array
     */
    private function getByAvailabilities(array $providers, ?bool $createStep = true): array
    {
        $providers = $this->matchingRepository->getByAvailabilities($providers, $this->shift);
        if ($createStep) {
            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::AVAILABILITIES,
                $providers
            );
        }
        return $providers;
    }

    /**
     * @param array $providers
     * @param bool|null $createStep
     * @return array
     */
    private function getByHired(array $providers, ?bool $createStep = true): array
    {
        $providers = $this->matchingRepository->getByHired($providers, $this->shift);
        if ($createStep) {
            $this->matchingStepsRepository->createStep(
                $this->shift->id,
                $this->try,
                MatchingSteps::BY_HIRED,
                $providers
            );
        }

        return $providers;
    }

    /**
     * @param array $providers
     * @param int $practiceId
     * @return array
     */
    private function getByReviews(array $providers, int $practiceId): array
    {
        $providers = $this->matchingRepository->getByProviderReviews($providers, $practiceId);
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::PROVIDER_REVIEWS,
            $providers
        );

        $providers = $this->matchingRepository->getByPracticeReviews($providers, $practiceId);
        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::PRACTICE_REVIEWS,
            $providers
        );
        return $providers;
    }

    /**
     * @param array $providers
     * @param float $averageStarsToProvider
     * @return array
     */
    private function getByAverage(array $providers, float $averageStarsToProvider): array
    {
        $providers = $this->matchingRepository->getByAverage($providers, $averageStarsToProvider);

        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::AVERAGE,
            $providers
        );
        return $providers;
    }

    /**
     * @param array $providers
     * @param Shift $shift
     * @return array
     */
    private function getByTasks(array $providers, Shift $shift): array
    {
        if (empty($shift->tasks)) {
            return $providers;
        }
        $providers = $this->matchingRepository->getByTasks($providers, $shift->tasks ?: []);

        $this->matchingStepsRepository->createStep(
            $this->shift->id,
            $this->try,
            MatchingSteps::BY_TASKS,
            $providers
        );
        return $providers;
    }
}
