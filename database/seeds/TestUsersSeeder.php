<?php

use App\Entities\Industry\Position;
use App\Entities\User\User;
use Illuminate\Database\Seeder;

/**
 * Class TestUsersSeeder
 */
class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $position = Position::where('title', 'Dentist')->first();
        if (!$position) {
            return;
        }
        foreach (self::PROVIDERS as $provider) {
            /** @var User $user */
            $user = User::where('email', $provider['email'])->first();
            if (!$user) {
                $user = User::create([
                    'email' => $provider['email'],
                    'first_name' => $provider['first_name'],
                    'last_name' => $provider['last_name'],
                    'phone' => $provider['phone'],
                    'password' => $provider['password'],
                    'status' => $provider['status']
                ]);
                $user->roles()->attach(3);
            }
            $user->specialist()->create([
                'industry_id' => $position->industry->id,
                'position_id' => $position->id,
                'driver_address' => $provider['specialict']['driver_address'],
                'driver_city' => $provider['specialict']['driver_city'],
                'driver_state' => $provider['specialict']['driver_state'],
                'driver_zip' => $provider['specialict']['driver_zip'],
            ]);
            foreach ($provider['specialict']['licenses'] as $license) {
                $user->specialist->licenses()->create([
                    'type' => $license['type'],
                    'state' => $license['state'],
                    'number' => $license['number'],
                    'expiration_date' => $license['expiration_date'],
                    'position' => $license['position'],
                ]);
            }
        }
    }

    const PROVIDERS = [
        [
            'email' => 'user1@gmail.com',
            'first_name' => 'User1',
            'last_name' => 'User1',
            'phone' => '+111111111111',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', //secret
            'status' => 'active',
            'specialict' => [
                'industry_id' => 1,
                'position_id' => 2,
                'driver_address' => '800 Cherokee Ave SE',
                'driver_city' => 'Atlanta',
                'driver_state' => 'GA',
                'driver_zip' => '30315',
                'licenses' => [
                    [
                        'type' => 2,
                        'number' => '1111111111',
                        'expiration_date' => '2020-01-01',
                        'position' => 0,
                        'state' => 'GA'
                    ]
                ]
            ]
        ],
        [
            'email' => 'user2@gmail.com',
            'first_name' => 'User2',
            'last_name' => 'User2',
            'phone' => '+22222222222',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', //secret
            'status' => 'active',
            'specialict' => [
                'industry_id' => 1,
                'position_id' => 2,
                'driver_address' => '410 Englewood Ave SE',
                'driver_city' => 'Atlanta',
                'driver_state' => 'GA',
                'driver_zip' => '30315',
                'licenses' => [
                    [
                        'type' => 2,
                        'number' => '213423413',
                        'expiration_date' => '2020-01-01',
                        'position' => 0,
                        'state' => 'GA'
                    ]
                ]
            ]
        ],
        [
            'email' => 'user3@gmail.com',
            'first_name' => 'User3',
            'last_name' => 'User3',
            'phone' => '+33333333333',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', //secret
            'status' => 'active',
            'specialict' => [
                'industry_id' => 1,
                'position_id' => 2,
                'driver_address' => '1015 Grant St SE',
                'driver_city' => 'Atlanta',
                'driver_state' => 'GA',
                'driver_zip' => '30315',
                'licenses' => [
                    [
                        'type' => 2,
                        'number' => '3243421343',
                        'expiration_date' => '2020-01-01',
                        'position' => 0,
                        'state' => 'GA'
                    ]
                ]
            ]
        ],
        [
            'email' => 'user4@gmail.com',
            'first_name' => 'User4',
            'last_name' => 'User4',
            'phone' => '+4444444444',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', //secret
            'status' => 'active',
            'specialict' => [
                'industry_id' => 1,
                'position_id' => 2,
                'driver_address' => '2914 Memorial Dr SE',
                'driver_city' => 'Atlanta',
                'driver_state' => 'GA',
                'driver_zip' => '30317',
                'licenses' => [
                    [
                        'type' => 2,
                        'number' => '3412545234',
                        'expiration_date' => '2020-01-01',
                        'position' => 0,
                        'state' => 'GA'
                    ]
                ]
            ]
        ],
        [
            'email' => 'user5@gmail.com',
            'first_name' => 'User5',
            'last_name' => 'User5',
            'phone' => '+5555555555',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', //secret
            'status' => 'active',
            'specialict' => [
                'industry_id' => 1,
                'position_id' => 2,
                'driver_address' => '1220 Stadium Dr',
                'driver_city' => 'Macon',
                'driver_state' => 'GA',
                'driver_zip' => '31204',
                'licenses' => [
                    [
                        'type' => 2,
                        'number' => '67467456745346564567',
                        'expiration_date' => '2020-01-01',
                        'position' => 0,
                        'state' => 'GA'
                    ]
                ]
            ]
        ],
        [
            'email' => 'user6@gmail.com',
            'first_name' => 'User6',
            'last_name' => 'User6',
            'phone' => '+4444444444',
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', //secret
            'status' => 'active',
            'specialict' => [
                'industry_id' => 1,
                'position_id' => 2,
                'driver_address' => '753 Cherokee Ave SE',
                'driver_city' => 'Atlanta',
                'driver_state' => 'GA',
                'driver_zip' => '30315',
                'licenses' => [
                    [
                        'type' => 2,
                        'number' => '341254342345234',
                        'expiration_date' => '2020-01-01',
                        'position' => 0,
                        'state' => 'IL'
                    ]
                ]
            ]
        ],
    ];
}
