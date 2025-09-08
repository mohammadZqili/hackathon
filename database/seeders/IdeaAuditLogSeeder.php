<?php

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\User;
use Illuminate\Database\Seeder;

class IdeaAuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ideas = Idea::all();
        $users = User::all();
        
        if ($ideas->isEmpty() || $users->isEmpty()) {
            $this->command->warn('No ideas or users found. Please run IdeaSeeder and UserSeeder first.');
            return;
        }

        foreach ($ideas as $idea) {
            $user = $users->random();
            
            // Log idea creation
            $idea->auditLogs()->create([
                'user_id' => $idea->team->leader_id ?? $user->id,
                'action' => 'created',
                'field_name' => null,
                'old_value' => null,
                'new_value' => 'draft',
                'notes' => 'Idea was created',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Seeder)',
                'created_at' => $idea->created_at,
                'updated_at' => $idea->created_at,
            ]);

            // Log submission if submitted
            if (in_array($idea->status, ['submitted', 'under_review', 'accepted', 'rejected', 'needs_revision'])) {
                $idea->auditLogs()->create([
                    'user_id' => $idea->team->leader_id ?? $user->id,
                    'action' => 'status_changed',
                    'field_name' => 'status',
                    'old_value' => 'draft',
                    'new_value' => 'submitted',
                    'notes' => 'Idea submitted for review',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (Seeder)',
                    'created_at' => $idea->submitted_at ?? $idea->created_at->addHours(1),
                    'updated_at' => $idea->submitted_at ?? $idea->created_at->addHours(1),
                ]);
            }

            // Log review if reviewed
            if (in_array($idea->status, ['under_review', 'accepted', 'rejected', 'needs_revision']) && $idea->reviewed_by) {
                $idea->auditLogs()->create([
                    'user_id' => $idea->reviewed_by,
                    'action' => 'status_changed',
                    'field_name' => 'status',
                    'old_value' => 'submitted',
                    'new_value' => $idea->status,
                    'notes' => $idea->feedback ?? 'Idea reviewed',
                    'metadata' => $idea->evaluation_scores ? ['scores' => $idea->evaluation_scores] : null,
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (Seeder)',
                    'created_at' => $idea->reviewed_at ?? $idea->created_at->addDays(1),
                    'updated_at' => $idea->reviewed_at ?? $idea->created_at->addDays(1),
                ]);
            }
        }

        $this->command->info('Idea audit logs seeded successfully!');
    }
}
