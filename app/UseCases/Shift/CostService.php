<?php

declare(strict_types=1);

namespace App\UseCases\Shift;

use App\Entities\Data\Distance;
use App\Entities\Data\Location\City;
use App\Entities\Industry\Position;
use App\Entities\Shift\Shift;
use App\Entities\User\Practice\AddressDTO;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Repositories\Data\DistanceRepository;
use App\Repositories\Data\Location\CityRepository;
use App\Repositories\Shift\Matching\MatchingRepository;
use App\Repositories\Statistics\MatchingStepsRepository;
use App\Services\Maps\DistanceService;

/**
 * Class CostService
 * Calculates cost for shift base on tier, distance and surge price.
 *
 * @package App\UseCases\Shift
 */
class CostService
{
    public const DEFAULT_TIER = 1.15;

    public const DEFAULT_PLACE_TIER = 1;

    /**
     * @var CityRepository
     */
    private $cityRepository;
    /**
     * @var MatchingRepository
     */
    private $matchingRepository;
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
    private $steps;

    /**
     * CostService constructor.
     * @param CityRepository $cityRepository
     * @param MatchingRepository $matchingRepository
     * @param DistanceService $distanceService
     * @param DistanceRepository $distanceRepository
     * @param MatchingStepsRepository $matchingStepsRepository
     */
    public function __construct(
        CityRepository $cityRepository,
        MatchingRepository $matchingRepository,
        DistanceService $distanceService,
        DistanceRepository $distanceRepository,
        MatchingStepsRepository $matchingStepsRepository
    ) {
        $this->cityRepository = $cityRepository;
        $this->matchingRepository = $matchingRepository;
        $this->distanceService = $distanceService;
        $this->distanceRepository = $distanceRepository;
        $this->steps = $matchingStepsRepository;
    }

    /**
     * @param Shift $shift
     * @param bool|null $calculateSurge
     * @return array
     */
    public function getCosts(Shift $shift, ?bool $calculateSurge = true): array
    {
        /** @var Position $position */
        $position = $shift->position;

        $surgePrice = $shift->surge_price;
        if ($calculateSurge) {
            $surgePrice = 0;
            try {
                $surgePrice = $this->getSurgePrice($shift);
            } catch (\Exception $e) {
                \LogHelper::error($e, ['message' => 'Surge price calculation error', 'shift' => $shift]);
            }
            $shift->update([
                'surge_price' => $surgePrice
            ]);
        }

        /** @var City $city */
        $city = $this->cityRepository->getByName($shift->practice->city, $shift->practice->state);

        $tier = self::DEFAULT_TIER;
        $placeTier = self::DEFAULT_PLACE_TIER;
        if (isset($city->tier)) {
            $placeTier = $city->getTier->multiplier;
        } elseif (isset($city->area)) {
            $placeTier = $city->area->getTier->multiplier;
        }

        $costForProvider = ($position->fee * $shift->hours() + $surgePrice) * $placeTier;
        $rateFee = $position->fee;
        $minimumProfit = $position->minimum_profit;
        if ($rate = $shift->practice->rateWithPos($position->id)) {
            $rateFee = $rate->position->rate;
            $minimumProfit = $rate->position->minimum_profit;
        }
        $cost = $calculateSurge
            ? ($rateFee * $shift->hours() + $surgePrice) * $placeTier
            : ($rateFee * $shift->hours()) * $placeTier;

        $costForPractice = $cost * $tier;
        $costForPractice = (($costForPractice - $cost) >= $minimumProfit)
            ? $costForPractice
            : $costForPractice + ($minimumProfit - ($costForPractice - $cost));
        if ($rate && $shift->hours() == 8 && ($costForPractice > $rate->position->max_day_rate)) {
            $costForPractice = $rate->position->max_day_rate;
        }
        return [
            'cost' => round($costForProvider + $shift->bonus, 2) ,
            'costForPractice' => round($costForPractice, 2)
        ];
    }

    /**
     * @param Shift $shift
     * @return float
     */
    private function getSurgePrice(Shift $shift)
    {
        $this->steps->createStep($shift->id, 0, 'Surge price calculation start', []);
        if ($this->isAvailableProvidersInRadiusExists($shift)) {
            $this->steps->createStep($shift->id, 0, 'Available providers in radius exists', ['surge_price' => 0]);
            return 0;
        }
        if ($this->isNotAvailableProvidersInRadiusExists($shift)) {
            $surgePrice = $shift->position->surge_price;
            if ($rate = $shift->practice->rateWithPos($shift->position_id)) {
                $surgePrice = $rate->position->surge_price;
            }
            //$this->steps->createStep(
            //$shift->id, 0, 'Not available providers in radius exists', ['surge_price' => $surgePrice]
            //);
            //return $surgePrice;
            $this->steps->createStep(
                $shift->id,
                0,
                'Not available providers in radius exists. Now without surge price.',
                ['surge_price' => $surgePrice]
            );
            return 0;
        }
        $closestPrice = $this->getClosestDistanceAverageCalculated($shift);
        if (!$closestPrice) {
            return 0;
        }
        $this->steps->createStep(
            $shift->id,
            0,
            'Available providers out of radius',
            ['surge_price' => $closestPrice]
        );
        return $closestPrice;
    }

    /**
     * @param Shift $shift
     * @return bool
     */
    private function isAvailableProvidersInRadiusExists(Shift $shift): bool
    {
        $this->steps->createStep($shift->id, 0, 'Starting search available providers in radius', []);
        $creator = $shift->creator;
        $isTest = (bool)($creator->is_test_account ?? false);
        $providers = $this->matchingRepository->getBaseFoundProviders(
            $shift->position_id,
            $shift->practice_location->state,
            [],
            $isTest
        );
        if (empty($providers)) {
            return false;
        }
        $providers = $this->matchingRepository->getByHired($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $providers = $this->matchingRepository->getByHolidays($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $providers = $this->matchingRepository->getByAvailabilities($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $providers = $this->getByDistance($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $this->steps->createStep($shift->id, 0, 'Result for available providers in radius', $providers);
        return true;
    }

    /**
     * @param array $providers
     * @param Shift $shift
     * @return bool
     */
    public function getByDistance(array $providers, Shift $shift): array
    {
        $providers = Specialist::whereIn('user_id', $providers)->get();
        $addresses = [];
        $providersWithoutAddress = [];
        $practice = $shift->practice;
        $practiceLocation = $shift->practice_location;
        foreach ($providers as $provider) {
            $distance = $this->distanceRepository->findFullDistance(
                $provider->user_id,
                $practice->id,
                $practiceLocation->addressId
            );
            if (!$distance) {
                $addresses[] = $provider->full_address;
                $providersWithoutAddress[] = $provider->user_id;
            }
        }
        if (!empty($addresses)) {
            $distances = $this->distanceService->getDistances($practiceLocation->fullAddress(), $addresses);
            for ($i = 0; $i < count($distances); $i++) {
                if (!$distances[$i]) {
                    continue;
                }
                $this->distanceRepository->createDistance(
                    $providersWithoutAddress[$i],
                    $practice->id,
                    $practiceLocation->addressId,
                    $distances[$i]
                );
            }
        }
        $amount = 0;
        $needleProviders = [];
        foreach ($providers as $provider) {
            $distance = $this->distanceRepository->findFullDistance(
                $provider->user_id,
                $practice->id,
                $practiceLocation->addressId
            );
            if (!$distance) {
                continue;
            }
            if ($provider->shift_duration_max && ($distance->duration / 60 <= $provider->shift_duration_max)) {
                $amount++;
                $needleProviders[] = $provider->user_id;
            } elseif ($provider->shift_distance_max && ($distance->distance / 1000 <= $provider->shift_distance_max)) {
                $amount++;
                $needleProviders[] = $provider->user_id;
            } elseif ($distance->duration <= MatchingService::DISTANCE_LIMIT_2) {
                $amount++;
                $needleProviders[] = $provider->user_id;
            }
        }

        return $needleProviders;
    }

    /**
     * @param Shift $shift
     * @return bool
     */
    private function isNotAvailableProvidersInRadiusExists(Shift $shift): bool
    {
        $this->steps->createStep($shift->id, 0, 'Starting search not available providers in radius', []);
        $creator = $shift->creator;
        $isTest = (bool)($creator->is_test_account ?? false);
        $providers = $this->matchingRepository->getBaseFoundProvidersWithoutAvailability(
            $shift->position_id,
            $shift->practice_location->state,
            [],
            $isTest
        );
        if (empty($providers)) {
            return false;
        }
        $providers = $this->matchingRepository->getByHired($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $providers = $this->matchingRepository->getByHolidays($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $availableProviders = $this->matchingRepository->getByAvailabilities($providers, $shift);
        $providers = array_diff($providers, $availableProviders);
        if (empty($providers)) {
            return false;
        }
        $providers = $this->getByDistance($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $this->steps->createStep($shift->id, 0, 'Result for not available providers in radius', $providers);
        return true;
    }

    /**
     * @param Shift $shift
     * @return bool|float
     */
    private function getClosestDistanceAverageCalculated(Shift $shift)
    {
        $this->steps->createStep($shift->id, 0, 'Starting search closest providers out of radius', []);
        $creator = $shift->creator;
        $isTest = (bool)($creator->is_test_account ?? false);
        $providers = $this->matchingRepository->getBaseFoundProviders(
            $shift->position_id,
            $shift->practice_location->state,
            [],
            $isTest
        );
        if (empty($providers)) {
            return false;
        }
        $providers = $this->matchingRepository->getByHired($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $providers = $this->matchingRepository->getByHolidays($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $providers = $this->matchingRepository->getByAvailabilities($providers, $shift);
        if (empty($providers)) {
            return false;
        }
        $distances = $this->distanceRepository->findForShift($shift, $providers);
        if ($distances->count() == DistanceRepository::OTHER_AREA_LIMIT) {
            $needleDistance = 0;
            $needleProviders = [];
            foreach ($distances as $distance) {
                $needleDistance += $distance->distance / 100;
                $needleProviders[] = $distance->provider_id;
            }
            $this->steps->createStep($shift->id, 0, 'Closest providers out of radius', $needleProviders);
            return round((($needleDistance * 0.621371) - 15) * 0.58, 2);
        }
        $this->steps->createStep($shift->id, 0, 'Closest providers out of radius not found', []);
        return false;
    }
}
