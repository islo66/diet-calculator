<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => User::SYSTEM_EMAIL],
            [
                'name' => 'System User',
                'password' => 'PLKJHGFDSA',
                'locale' => 'ro',
                'email_verified_at' => now(),
            ]
        );
    }
}
