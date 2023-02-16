<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::firstOrCreate([
            'email' => 'admin@foodics.com',
        ], [
            'name' => 'Admin User',
            'email' => 'admin@foodics.com',
            'email_verified_at' => now(),
            'password' => Hash::make('P@55w0rd@Foodics'), // password
            'remember_token' => Str::random(10),
        ]);

    }
}
