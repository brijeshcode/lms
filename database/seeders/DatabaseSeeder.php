<?php

use Database\Seeders\ChapterSeeder;
use Database\Seeders\StudentClassSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        // $this->call(ChapterSeeder::class);

        // $this->call(StudentClassSeeder::class);

    }
}