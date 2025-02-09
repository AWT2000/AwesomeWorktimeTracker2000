<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\dummies\DummyDataSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AclSeeder::class);
        $this->call(SuperAdminSeeder::class);
        $this->call(DummyDataSeeder::class);
    }
}
