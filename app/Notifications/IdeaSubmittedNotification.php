<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Idea;

class IdeaSubmittedNotification extends Notification
{
    use Queueable;

    protected $idea;

    /**
     * Create a new notification instance.
     */
    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
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
            'title' => 'Idea Submitted Successfully',
            'message' => "Your idea '{$this->idea->title}' has been submitted for review.",
            'priority' => 'high',
            'type' => 'idea',
            'idea_id' => $this->idea->id,
        ];
    }
}