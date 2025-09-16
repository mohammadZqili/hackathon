<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Idea;
use App\Models\User;

class IdeaSubmittedNotification extends Notification
{
    use Queueable;

    protected $idea;
    protected $submittedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Idea $idea, ?User $submittedBy = null)
    {
        $this->idea = $idea;
        $this->submittedBy = $submittedBy ?: $idea->team->leader;
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
        // Different message based on recipient
        $isSubmitter = $notifiable->id === $this->submittedBy?->id;
        $teamName = $this->idea->team?->name ?? 'the team';

        if ($isSubmitter) {
            $message = "You have successfully submitted the idea '{$this->idea->title}' for review.";
        } else {
            $submitterName = $this->submittedBy?->name ?? 'Team leader';
            $message = "{$submitterName} has submitted the idea '{$this->idea->title}' for team {$teamName}.";
        }

        return [
            'title' => 'New Idea Submitted',
            'message' => $message,
            'priority' => 'high',
            'type' => 'idea_submitted',
            'idea_id' => $this->idea->id,
            'team_id' => $this->idea->team_id,
            'team_name' => $teamName,
            'submitted_by_id' => $this->submittedBy?->id,
            'submitted_by_name' => $this->submittedBy?->name,
            'idea_title' => $this->idea->title,
        ];
    }
}