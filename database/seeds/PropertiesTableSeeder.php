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
            'name' => 'The Pacific,Lagos',
            'slug' => 'the-pacific-lagos',
            'image' => 'https://res.cloudinary.com/altdotng/image/upload/v1590244077/properties/property-image_unmoef.png',
            'about' => 'It’s a blend of commercial,
                        luxury residential and play space development of two distinct 
                        luxury towers – Commercial and Residential.  
                        It features 3 levels of parking space, a recreational floor, 
                        10 floors on the commercial block and 12 floors on the residential
                        block.  It is a fully serviced self sufficient structure that 
                        will be equipped with state of the art facilities.  
                        There will be access to recreational facilities such as: 
                        swimming pool, gym & spa, open terraces, bar, restaurant 
                        and a roof top viewing & relaxation area.  It will also be 
                        equipped with air conditioning system, water reticulation and 
                        sewage & waste treatment plant among others.',
            'brochure' => 'https://res.cloudinary.com/altdotng/image/upload/v1590243044/properties_brochure/THE_PACIFIC_LAGOS_REV_oaposl.pdf',
            'location' => '20, Ozumba Mbadiwe Avenue, Victoria Island, Lagos.',
            'investment_population' => 1000,
            'net_rental_yield' => ['6' => 6, '12' => 12],
            'min_yield' => 6,
            'max_yield' => 15,
            'holding_period' => ['6', '12'],
            'min_fraction_price' => 100000,
            'max_fraction_price' => 20000000,
            'category_id' => 1,
            'gallery' => [
                'https://res.cloudinary.com/altdotng/image/upload/v1590244329/properties/living-room-rendering_zvzv18.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244320/properties/rest-room-rendering_px28rm.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244320/properties/external-rendering-2_rs86m0.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244317/properties/external-rendering_ulalkv.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244316/properties/show-room_jtegum.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244314/properties/restaurant_qvoc78.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244310/properties/packing-floor_xvilon.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244311/properties/packing-floor-3_gm5nfu.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244310/properties/packing-floor-2_ezm9r2.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244309/properties/external-rendering-3_tcswoi.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244308/properties/floor-plan-2_pavu2v.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244308/properties/lobby-view_smn0kg.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244307/properties/cross-section_d2ymr3.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244305/properties/groundbreaking-ceremony_ae7fhp.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244301/properties/floor-plan_g1zevl.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244297/properties/dining-room-rendering_hqgde7.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244296/properties/kitchen_n8uvur.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244296/properties/bedroom-rendering_vyab72.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244295/properties/kitchen-rendering_tdu0bm.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244295/properties/spa_oncy3j.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244293/properties/bedroom_jmf775.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244293/properties/living-room_pkndxy.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244293/properties/gymnasium_n0swk8.png',
                'https://res.cloudinary.com/altdotng/image/upload/v1590244077/properties/property-image_unmoef.png',
            ],
            'facility' => [
                'A 143-car parking garage', 'Games Room', 'Shopping mart',
                '4 elevators', 'Gymnasium', 'Spa/massage parlor', 'Restaurant and bar',
                'Terrace for outdoor viewing of the surrounding', 'Swimming pool and Sit out'
            ],
        ]);
    }
}
