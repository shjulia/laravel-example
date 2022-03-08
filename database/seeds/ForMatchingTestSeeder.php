<?php

use App\Entities\Data\LicenseType;
use App\Entities\Data\Location\Area;
use App\Entities\Data\Location\City;
use App\Entities\Data\State;
use App\Entities\Industry\Industry;
use App\Entities\Industry\Position;
use App\Entities\User\Practice\Practice;
use App\Entities\User\Role;
use App\Entities\User\User;
use App\Helpers\EncryptHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Class ForMatchingTestSeeder
 */
class ForMatchingTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();
        try {
            $area = Area::create([
                'name' => 'Test area',
                'tier' => 1,
                'is_open' => 1,
                'state_id' => State::where('short_title', 'GA')->first()->id
            ]);
            $area->cities()->attach([City::where('name', 'Atlanta')->first()->id]);

            $user = User::create([
                'email' => 'practicetest1@gmail.com',
                'first_name' => 'PracticeTest1',
                'last_name' => 'PracticeTest1',
                'phone' => '+380000000000',
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
                'status' => User::ACTIVE
            ]);
            $industryId = Industry::where('title', 'Dental')->first()->id;
            $positionId = Position::where('title', 'Dentist')->first()->id;
            $licenseId = LicenseType::first()->id;
            $practice = Practice::create([
                'industry_id' => $industryId,
                'practice_name' => 'PracticeTest Highland Yoga',
                'address' => '332 Ormond St SE',
                'city' => 'Atlanta',
                'state' => 'GA',
                'zip' => '30315',
                'area_id' => $area->id,
                'stripe_client_id' => EncryptHelper::encrypt(Str::uuid()),
                'lat' => 33.7316334,
                'lng' => -84.3767904
            ]);
            $user->practices()->attach($practice->id, [
                'is_creator' => 1,
                'practice_role' => Role::PRACTICE_ADMINISTRATOR
            ]);
            $user->roles()->attach(Role::where('title', 'Practice')->first()->id);
            $user->referral()->create(['referral_code' => 'dsdasdasdadvcxvz', 'referred_amount' => 0, 'referral_money_earned' => 0]);

            $providers = [
                [
                    'email' => 'testprovider1@gmail.com',
                    'first_name' => 'TestUser1',
                    'last_name' => 'User1',
                    'phone' => '+111111111111',
                    'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', //secret
                    'status' => 'active',
                    'specialict' => [
                        'industry_id' => $industryId,
                        'position_id' => $positionId,
                        'driver_address' => '800 Cherokee Ave SE',
                        'driver_city' => 'Atlanta',
                        'driver_state' => 'GA',
                        'driver_zip' => '30315',
                        'area_id' => $area->id,
                        'lat' => 33.734098,
                        'lng' => -84.372268,
                        'licenses' => [
                            [
                                'type' => $licenseId,
                                'number' => '1111111111',
                                'expiration_date' => '2020-01-01',
                                'position' => 0,
                                'state' => 'GA'
                            ]
                        ],
                        'times' => [
                            'days' => [[1,2,3,4,5,6,7]],
                            'from' => ['09:00'],
                            'to' => ['20:00']
                        ]
                    ]
                ],
                [
                    'email' => 'testprovider2@gmail.com',
                    'first_name' => 'TestUser2',
                    'last_name' => 'User2',
                    'phone' => '+22222222222',
                    'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', //secret
                    'status' => 'active',
                    'specialict' => [
                        'industry_id' => $industryId,
                        'position_id' => $positionId,
                        'driver_address' => '410 Englewood Ave SE',
                        'driver_city' => 'Atlanta',
                        'driver_state' => 'GA',
                        'driver_zip' => '30315',
                        'area_id' => $area->id,
                        'lat' => 33.7222589,
                        'lng' => -84.3750098,
                        'licenses' => [
                            [
                                'type' => $licenseId,
                                'number' => '213423413',
                                'expiration_date' => '2020-01-01',
                                'position' => 0,
                                'state' => 'GA'
                            ]
                        ],
                        'times' => [
                            'days' => [[1,2,3,4,5,6,7]],
                            'from' => ['09:00'],
                            'to' => ['20:00']
                        ]
                    ]
                ],
                [
                    'email' => 'testprovider3@gmail.com',
                    'first_name' => 'TestUser3',
                    'last_name' => 'User3',
                    'phone' => '+33333333333',
                    'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', //secret
                    'status' => 'active',
                    'specialict' => [
                        'industry_id' => $industryId,
                        'position_id' => $positionId,
                        'driver_address' => '1015 Grant St SE',
                        'driver_city' => 'Atlanta',
                        'driver_state' => 'GA',
                        'driver_zip' => '30315',
                        'area_id' => $area->id,
                        'lat' => 33.7267495,
                        'lng' => -84.3770609,
                        'licenses' => [
                            [
                                'type' => $licenseId,
                                'number' => '3243421343',
                                'expiration_date' => '2020-01-01',
                                'position' => 0,
                                'state' => 'GA'
                            ]
                        ],
                        'times' => [
                            'days' => [[1,2,3,4,5,6,7]],
                            'from' => ['09:00'],
                            'to' => ['13:00']
                        ]
                    ]
                ],
            ];

            $providerRole = Role::where('type', 'provider')->first();
            foreach ($providers as $provider) {
                /** @var User $user */
                $user = User::create([
                    'email' => $provider['email'],
                    'first_name' => $provider['first_name'],
                    'last_name' => $provider['last_name'],
                    'phone' => $provider['phone'],
                    'password' => $provider['password'],
                    'status' => $provider['status']
                ]);
                $user->specialist()->create([
                    'industry_id' => $provider['specialict']['industry_id'],
                    'position_id' => $provider['specialict']['position_id'],
                    'driver_address' => $provider['specialict']['driver_address'],
                    'driver_city' => $provider['specialict']['driver_city'],
                    'driver_state' => $provider['specialict']['driver_state'],
                    'driver_zip' => $provider['specialict']['driver_zip'],
                    'area_id' => $provider['specialict']['area_id'],
                    'lat' => $provider['specialict']['lat'],
                    'lng' => $provider['specialict']['lng'],
                    'available' => 1
                ]);
                $user->roles()->attach($providerRole->id);
                $user->specialist->checkr()->create();
                foreach ($provider['specialict']['licenses'] as $license) {
                    $user->specialist->licenses()->create([
                        'type' => $license['type'],
                        'state' => $license['state'],
                        'number' => $license['number'],
                        'expiration_date' => $license['expiration_date'],
                        'position' => $license['position'],
                    ]);
                }
                $i = 0;
                foreach ($provider['specialict']['times']['days'][0] as $day) {
                    $user->specialist->availabilities()->create([
                        'from_hour' => $provider['specialict']['times']['from'][$i],
                        'to_hour' => $provider['specialict']['times']['to'][$i],
                        'day' => $day
                    ]);
                }
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }
}
