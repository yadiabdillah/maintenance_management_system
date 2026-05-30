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
            'role' => 'Super Admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Supervisor Slamet',
            'email' => 'slamet@mms.com',
            'password' => Hash::make('password123'),
            'role' => 'Supervisor',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Mekanik Budi',
            'email' => 'budi@mms.com',
            'password' => Hash::make('password123'),
            'role' => 'Operator',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Operator Gudang Ani',
            'email' => 'ani@mms.com',
            'password' => Hash::make('password123'),
            'role' => 'Operator',
            'is_active' => true,
        ]);
    }
}
