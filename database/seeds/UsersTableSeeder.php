<?php

use Illuminate\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //admin
        App\User::create([
            'firstname' => 'Ayokunle',
            'lastname' => 'Adesanya',
            'email' => 'adesanyaayokunle@gmail.com',
            'password' => app('hash')->make('password'),
            'referral_token' =>  Uuid::uuid1(),
            'is_verified' => 1,
            'admin' => 1,
            'avatar' => 'https://res.cloudinary.com/altdotng/image/upload/v1584185412/alt_avatars/default-profile_an4tnd.png'
        ]);
        //not admin
        App\User::create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'user@gmail.com',
            'password' => app('hash')->make('password'),
            'referral_token' =>  Uuid::uuid1(),
            'is_verified' => 1,
            'admin' => 0,
            'avatar' => 'https://res.cloudinary.com/altdotng/image/upload/v1584185412/alt_avatars/default-profile_an4tnd.png'
        ]);
    }
}
