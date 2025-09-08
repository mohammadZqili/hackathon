<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::firstOrCreate(
            ['id' => 1], // Use ID as identifier
            [
                'passwordless_login' => false,
                'password_expiry' => false,
                'two_factor_authentication' => false,
            ]
        );
    }
}
