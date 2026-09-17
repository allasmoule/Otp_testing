<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StakeholderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['phone' => '110071'],
            [
                'name' => 'Main Stakeholder',
                'email' => 'stakeholder110071@botbari.com',
                'password' => Hash::make('Orn@@@@1234'),
                'role' => 'stakeholder',
                'is_otp_verified' => true,
            ]
        );
    }
}
