<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Idea;

class IdeaStatusChangedNotification extends Notification
{
    use Queueable;

    protected $idea;
    protected $oldStatus;
    protected $newStatus;
    protected $changedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Idea $idea, string $oldStatus, string $newStatus, $changedBy = null)
    {
        $this->idea = $idea;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->changedBy = $changedBy;
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
        $statusLabels = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under Review',
            'needs_revision' => 'Needs Revision',
            'approved' => 'Approved',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'pending_review' => 'Pending Review',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ];

        $oldLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
        $newLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        $changedByName = $this->changedBy ? $this->changedBy->name : 'System';

        return [
            'title' => 'Idea Status Updated',
            'message' => "The status of idea '{$this->idea->title}' has been changed from {$oldLabel} to {$newLabel} by {$changedByName}",
            'priority' => in_array($this->newStatus, ['approved', 'rejected']) ? 'high' : 'medium',
            'type' => 'idea_status_changed',
            'idea_id' => $this->idea->id,
            'team_id' => $this->idea->team_id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'changed_by_id' => $this->changedBy ? $this->changedBy->id : null,
            'changed_by_name' => $changedByName,
            'idea_title' => $this->idea->title,
        ];
    }
}