<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed 5 admin users
        User::factory()
            ->count(5)
            ->state(['is_admin' => true])
            ->create();

        // Seed 5 non-admin users
        User::factory()
            ->count(5)
            ->state(['is_admin' => false])
            ->create();

        User::create([
            'name' => 'Test Admin',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
    }
}
