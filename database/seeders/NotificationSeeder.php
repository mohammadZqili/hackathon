<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\Idea;
use App\Notifications\WelcomeNotification;
use App\Notifications\TeamJoinedNotification;
use App\Notifications\IdeaSubmittedNotification;
use Illuminate\Support\Facades\Notification;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        foreach ($users as $user) {
            // Send welcome notification
            $user->notify(new WelcomeNotification());
            
            // Add some generic notifications
            $genericNotifications = [
                [
                    'title' => 'Hackathon Registration Opens',
                    'message' => 'Registration for the upcoming hackathon is now open! Register your team before the deadline.',
                    'priority' => 'high',
                    'type' => 'announcement',
                ],
                [
                    'title' => 'Workshop Schedule Released',
                    'message' => 'Check out the workshop schedule and register for sessions you want to attend.',
                    'priority' => 'normal',
                    'type' => 'workshop',
                ],
                [
                    'title' => 'System Maintenance',
                    'message' => 'Scheduled maintenance will be performed tonight from 2 AM to 4 AM.',
                    'priority' => 'low',
                    'type' => 'system',
                ],
            ];
            
            foreach ($genericNotifications as $notificationData) {
                \DB::table('notifications')->insert([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'type' => 'App\Notifications\GenericNotification',
                    'notifiable_type' => get_class($user),
                    'notifiable_id' => $user->id,
                    'data' => json_encode($notificationData),
                    'read_at' => rand(0, 1) ? now()->subHours(rand(1, 48)) : null,
                    'created_at' => now()->subHours(rand(1, 72)),
                    'updated_at' => now()->subHours(rand(1, 72)),
                ]);
            }
        }
        
        $this->command->info('Notifications seeded successfully!');
    }
}