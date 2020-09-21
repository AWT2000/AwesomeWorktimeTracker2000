<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@awesomeworktimetracker.com',
            'password' => Hash::make(env('ADMIN_PWD', 'secret')),
        ]);

        $user->assignRole('admin');
    }
}
