<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Team;
use App\Models\User;

class TeamJoinedNotification extends Notification
{
    use Queueable;

    protected $team;
    protected $member;

    /**
     * Create a new notification instance.
     */
    public function __construct(Team $team, User $member)
    {
        $this->team = $team;
        $this->member = $member;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Team Member',
            'message' => "{$this->member->name} has joined your team: {$this->team->name}",
            'priority' => 'normal',
            'type' => 'team',
            'team_id' => $this->team->id,
            'member_id' => $this->member->id,
        ];
    }
}