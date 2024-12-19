<?php

namespace Database\Seeders;

use App\Models\Admin;
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
        // User::factory(10)->create();

        Admin::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'phone_number' => '012165288',
            'image' => null,
            'id_image' => null,
            'password' => Hash::make('password'),
        ]);
    }
}
