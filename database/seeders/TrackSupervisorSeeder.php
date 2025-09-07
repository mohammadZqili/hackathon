<?php

namespace Database\Seeders;

use App\Models\Track;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrackSupervisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tracks = Track::all();
        $supervisors = User::where('user_type', 'track_supervisor')->get();

        if ($tracks->isEmpty() || $supervisors->isEmpty()) {
            // Create some tracks and supervisors if they don't exist
            if ($tracks->isEmpty()) {
                Track::factory()->count(3)->create();
                $tracks = Track::all();
            }
            if ($supervisors->isEmpty()) {
                User::factory()->trackSupervisor()->count(3)->create();
                $supervisors = User::where('user_type', 'track_supervisor')->get();
            }
        }

        foreach ($tracks as $track) {
            // Attach a random supervisor to each track
            $supervisor = $supervisors->random();
            $track->supervisors()->attach($supervisor->id, ['is_primary' => true]);

            // Optionally attach more supervisors
            $otherSupervisors = $supervisors->except($supervisor->id)->random(rand(0, min(2, $supervisors->count() - 1)));
            foreach ($otherSupervisors as $otherSupervisor) {
                // Check if already attached to avoid unique constraint violation
                if (!$track->supervisors->contains($otherSupervisor->id)) {
                    $track->supervisors()->attach($otherSupervisor->id, ['is_primary' => false]);
                }
            }
        }
    }
}
