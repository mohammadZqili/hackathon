<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Track;
use App\Services\TrackService;

class TestTrackSupervisorEdit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:track-supervisor-edit {track_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test track supervisor edit permissions';

    protected TrackService $trackService;

    public function __construct(TrackService $trackService)
    {
        parent::__construct();
        $this->trackService = $trackService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $trackId = $this->argument('track_id');

        // Find a track supervisor
        $trackSupervisor = User::role('track_supervisor')->first();

        if (!$trackSupervisor) {
            $this->error('No track supervisor found in the system.');
            return Command::FAILURE;
        }

        $this->info("Testing with track supervisor: {$trackSupervisor->name} (ID: {$trackSupervisor->id})");

        // Get the track
        $track = Track::find($trackId);
        if (!$track) {
            $this->error("Track with ID {$trackId} not found.");
            return Command::FAILURE;
        }

        $this->info("Track: {$track->name} (ID: {$track->id}, Edition: {$track->edition_id})");

        // Check if supervisor has access to this track's edition
        $editionId = $track->edition_id;
        $trackCount = $trackSupervisor->tracksInEdition($editionId)->count();
        $this->info("Supervisor has {$trackCount} tracks in edition {$editionId}");

        // Check if supervisor is assigned to this specific track
        $assignedTracks = $trackSupervisor->tracksInEdition($editionId)->pluck('tracks.id')->toArray();
        $isAssigned = in_array($track->id, $assignedTracks);
        $this->info("Is supervisor assigned to this track? " . ($isAssigned ? 'Yes' : 'No'));

        // Test the update
        try {
            $data = [
                'name' => $track->name . ' (Updated)',
                'description' => $track->description,
                'hackathon_id' => $track->hackathon_id,
                'edition_id' => $track->edition_id,
                'max_teams' => $track->max_teams,
                'is_active' => $track->is_active
            ];

            $this->info("Attempting to update track...");
            $result = $this->trackService->updateTrack($track->id, $data, $trackSupervisor);

            $this->info('✅ Success! Track updated successfully.');
            $this->info("Message: " . $result['message']);

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to update track: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}