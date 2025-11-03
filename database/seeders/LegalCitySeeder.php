<?php

namespace Database\Seeders;

use App\Models\LegalCity;
use Illuminate\Database\Seeder;

class LegalCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            [
                'id' => 1,
                'name' => 'Los Angeles',
                'locations' => [
                    64042 => '1754C Slauson Ave Los Angeles 90047',
                    64762 => '6216 S Vermont Ave Los Angeles 90044',
                    64763 => '181 W Manchester Ave Los Angeles 90003',
                    64764 => '1205 E Century Blvd Los Angeles 90002',
                    64765 => '611 E Imperial Hwy unit 108 Los Angeles 90059',
                    64776 => '6216 S Vermont Ave Los Angeles 90044',
                ],
            ],
            [
                'id' => 2,
                'name' => 'Inglewood',
                'locations' => [],
            ],
            [
                'id' => 3,
                'name' => 'Compton',
                'locations' => [
                    64767 => '930 N Long Beach Blvd unit 3 Compton 90221',
                ],
            ],
            [
                'id' => 4,
                'name' => 'Long Beach',
                'locations' => [
                    64768 => '4921 Long Beach Blvd Long Beach 90805',
                ],
            ],
            [
                'id' => 5,
                'name' => 'Lancaster',
                'locations' => [],
            ],
            [
                'id' => 6,
                'name' => 'Moreno Valley',
                'locations' => [
                    64774 => '23878 Sunnymead Blvd Moreno Valley 92553',
                ],
            ],
            [
                'id' => 7,
                'name' => 'Victorville',
                'locations' => [
                    64770 => '15770 Mojave Dr Suite G Victorville 92394',
                ],
            ],
            [
                'id' => 8,
                'name' => 'Adelanto',
                'locations' => [
                    64775 => '11336 Bartlett Ave Unit 6 Adelanto 92301',
                ],
            ],
            [
                'id' => 9,
                'name' => 'No Preference',
                'locations' => [
                    63263 => 'No Preference',
                ],
            ],
        ];

        foreach ($cities as $city) {
            $newCity = LegalCity::updateOrCreate(
                [
                    'id' => $city['id'],
                    'name' => $city['name'],
                ],
            );

            if (isset($city['locations'])) {
                foreach ($city['locations'] as $key => $location) {
                    $newCity->locations()->create([
                        'id' => $key,
                        'title' => $location,
                    ]);
                }
            }
        }
    }
}
