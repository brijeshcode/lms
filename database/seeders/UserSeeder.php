<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert(
            [
                'name' => "Admin",
                'email' => 'dev2@test.com',
                'email_verified_at' => now(),
                'password' => bcrypt('test'),
                'remember_token' => Str::random(10),
            ],
        );

        User::insert(
            [
                'name' => "Brijesh",
                'email' => 'dev@test.com',
                'email_verified_at' => now(),
                'password' => bcrypt('test'),
                'remember_token' => Str::random(10),
            ],
        );
    }
}
