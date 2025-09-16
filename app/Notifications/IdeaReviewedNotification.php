<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Idea;
use App\Models\User;

class IdeaReviewedNotification extends Notification
{
    use Queueable;

    protected $idea;
    protected $reviewer;
    protected $feedback;
    protected $status;

    /**
     * Create a new notification instance.
     */
    public function __construct(Idea $idea, User $reviewer, string $status, ?string $feedback = null)
    {
        $this->idea = $idea;
        $this->reviewer = $reviewer;
        $this->status = $status;
        $this->feedback = $feedback;
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
        $statusMessages = [
            'approved' => 'approved',
            'accepted' => 'accepted',
            'rejected' => 'rejected',
            'needs_revision' => 'requested changes for',
            'under_review' => 'started reviewing',
            'pending_review' => 'submitted for review',
        ];

        $action = $statusMessages[$this->status] ?? 'reviewed';

        return [
            'title' => 'Idea Review Update',
            'message' => "{$this->reviewer->name} has {$action} the idea '{$this->idea->title}'",
            'feedback' => $this->feedback,
            'priority' => in_array($this->status, ['approved', 'rejected']) ? 'high' : 'medium',
            'type' => 'idea_reviewed',
            'idea_id' => $this->idea->id,
            'team_id' => $this->idea->team_id,
            'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => $this->reviewer->name,
            'status' => $this->status,
            'idea_title' => $this->idea->title,
        ];
    }
}