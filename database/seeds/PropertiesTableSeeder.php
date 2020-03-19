<?php

use Illuminate\Database\Seeder;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Model\Property::create([
            'name' => 'Lagos Property',
            'image' => 'https://res.cloudinary.com/altdotng/image/upload/v1584185412/alt_avatars/default-profile_an4tnd.png',
            'about' => 'Lorem ipsum some nonsense here',
            'brochure' => 'https://res.cloudinary.com/altdotng/image/upload/v1584185412/alt_avatars/default-profile_an4tnd.png',
            'location' => 'Lorem ipsum the location here',
            'investment_population' => 1000,
            'net_rental_yield' => 1.24,
            'holding_period' => 1,
            'min_fraction_price' => 100000,
            'max_fraction_price' => 900000,
            'category_id' => 1,
            'gallery' => [
                'https://res.cloudinary.com/altdotng/image/upload/v1584185412/alt_avatars/default-profile_an4tnd.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1584185412/alt_avatars/default-profile_an4tnd.png'
            ],
            'facility' => ['Facility 1', 'Facility 2'],
            'video' => 'https://res.cloudinary.com/altdotng/image/upload/v1584185412/alt_avatars/default-profile_an4tnd.mp4'
        ]);
    }
}
