<?php

namespace App\Repositories\Admin\Analytics;

use App\Entities\Data\Location\Area;
use App\Entities\Industry\Position;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Provider\Specialist;
use App\Entities\User\Role;
use App\Entities\User\User;
use App\Http\Composers\DaysComposer;
use Illuminate\Support\Facades\DB;

/**
 * Class MapRepository
 * @package App\Repositories\Admin\Analytics
 */
class MapRepository
{
    /**
     * @return Area[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function signupsByAreas()
    {
        $areas = Area::with(['cities', 'zipCodes'])
            ->whereHas('specialists')
            ->orWhereHas('practices')
            ->withCount(['specialists', 'practices'])
            ->get();
        $areas->each(function ($area) {
            $area->setAppends(['geocode']);
        });
        return $areas;
    }

    /**
     * @return array
     */
    public function signups(): array
    {
        $practices = DB::table('practices')
            ->select([
                'id',
                'lat',
                'lng',
                DB::raw('practice_name as name')
            ])
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users')
                    ->join('user_practice', 'users.id', '=', 'user_practice.user_id')
                    ->whereRaw('practices.id = user_practice.practice_id')
                    ->where('users.is_test_account', '!=', 1)
                    ->where('users.is_rejected', 0);
            })
            ->where('lat', '!=', null)
            ->where('lng', '!=', null)
            ->get();

        $practiceAddresses = DB::table('practice_addresses')
            ->select([
                'id',
                'lat',
                'lng',
                DB::raw('practice_name as name')
            ])
            ->whereIn('practice_id', $practices->pluck('id')->toArray())
            ->where('lat', '!=', null)
            ->where('lng', '!=', null)
            ->get();

        $practices = $practices->toBase()->merge($practiceAddresses);

        $providers = DB::table('specialists')
            ->select([
                'lat',
                'lng',
                DB::raw("TRIM(CONCAT(users.first_name, ' ', users.last_name)) AS name"),
                DB::raw("user_id as id"),
                'available'
            ])
            ->leftJoin('users', 'specialists.user_id', '=', 'users.id')
            ->where('users.is_test_account', '!=', 1)
            ->where('users.is_rejected', 0)
            ->where('lat', '!=', null)
            ->where('lng', '!=', null)
            ->get();
        $availabilities = DB::table('provider_availabilities')
            ->whereIn('specialist_id', $providers->pluck('id')->toArray())
            ->get()->groupBy('specialist_id');

        $providers = $providers->map(function ($item, $key) use ($availabilities) {
            $item->availabilities = $availabilities[$item->id] ?? null;
            return $item;
        });

        return [
            'practices' => $practices,
            'providers' => $providers
        ];
    }

    /**
     * @param string|null $state
     * @return array
     */
    public function findAvailable(?string $state): array
    {
        $list = [];
        $days = DaysComposer::DAYS;
        $column = 0;
        $positions = Position::get();
        $list[0][0] = "";
        foreach ($positions as $position) {
            $list[0][$column + 1] = $position->title;
            $positionAv = $this->findAvailableByPosition($position->id, $state);
            for ($i = 0; $i < 7; $i++) {
                $list[$i + 1][$column + 1] = $positionAv[$i + 1]->providers_count ?? 0;//rand(0,3);//0;
            }
            $column++;
        }
        for ($i = 0; $i < 7; $i++) {
            $list[$i + 1][0] = $days[$i + 1];
        }
        foreach ($list as &$row) {
            ksort($row);
        }
        unset($row);
        return $list;
    }

    /**
     * @param int $position
     * @param string|null $state
     * @return \Illuminate\Support\Collection
     */
    private function findAvailableByPosition(int $position, ?string $state)
    {
        $availabilities = DB::table('provider_availabilities')
            ->select([
                DB::raw("count(distinct provider_availabilities.specialist_id) as providers_count"),
                'provider_availabilities.day'
            ])
            ->leftJoin('specialists', 'provider_availabilities.specialist_id', '=', 'specialists.user_id')
            ->leftJoin('users', 'specialists.user_id', '=', 'users.id')
            ->where('users.is_test_account', '!=', 1)
            ->where('users.is_rejected', 0)
            ->where('specialists.position_id', $position);
        if ($state) {
            $availabilities->where('specialists.driver_state', $state);
        }
        $availabilities = $availabilities->groupBy('provider_availabilities.day')
            ->get()
            ->keyBy('day');
        return $availabilities;
    }
}
