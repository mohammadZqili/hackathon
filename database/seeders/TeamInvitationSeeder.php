<?php

namespace Database\Seeders;

use App\Models\TeamInvitation;
use Illuminate\Database\Seeder;

class TeamInvitationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeamInvitation::factory()->count(10)->create();
    }
}
