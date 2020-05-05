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
            'name' => 'Dummy Property',
            'slug' => 'dummy-property',
            'image' => 'https://res.cloudinary.com/altdotng/image/upload/v1588259541/alt_avatars/construction_dnf9rv.jpg',
            'about' => 'The description of the property',
            'brochure' => 'https://res.cloudinary.com/altdotng/image/upload/v1588259541/alt_avatars/construction_dnf9rv.jpg',
            'location' => 'The location of the property',
            'investment_population' => 1000,
            'net_rental_yield' => 12,
            'min_yield' => 11,
            'max_yield' => 15,
            'holding_period' => 1,
            'min_fraction_price' => 1000,
            'max_fraction_price' => 9000,
            'category_id' => 1,
            'gallery' => [
                'https://res.cloudinary.com/altdotng/image/upload/v1588259541/alt_avatars/construction_dnf9rv.jpg',
                'https://res.cloudinary.com/altdotng/image/upload/v1588259541/alt_avatars/construction_dnf9rv.jpg'
            ],
            'facility' => ['Facility 1', 'Facility 2'],
            'video' => 'https://res.cloudinary.com/altdotng/image/upload/v1588259541/alt_avatars/construction_dnf9rv.jpg'
        ]);
    }
}
