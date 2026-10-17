<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create your admin account
        User::firstOrCreate(
            ['email' => 'verindra9142@gmail.com'],
            [
                'name' => 'verindrahp',
                'password' => Hash::make('admin123'),
                'is_admin' => true,
            ]
        );
    }
}
