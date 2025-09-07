<?php

namespace Database\Seeders;

use App\Models\Hackathon;
use Illuminate\Database\Seeder;

class HackathonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Hackathon::factory()->count(5)->create();
    }
}
