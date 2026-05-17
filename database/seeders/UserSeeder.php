<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator MMS',
            'email' => 'admin@mms.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Mekanik Budi',
            'email' => 'budi@mms.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
