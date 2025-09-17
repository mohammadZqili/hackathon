<?php

namespace App\Notifications;

use App\Mail\WorkshopRegistrationMail;
use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkshopCreatedNotification extends Notification
{
    use Queueable;

    protected Workshop $workshop;
    protected ?WorkshopRegistration $registration;

    /**
     * Create a new notification instance.
     */
    public function __construct(Workshop $workshop, ?WorkshopRegistration $registration = null)
    {
        $this->workshop = $workshop;
        $this->registration = $registration;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): WorkshopRegistrationMail
    {
        return new WorkshopRegistrationMail(
            $this->workshop,
            $notifiable,
            $this->registration?->id
        );
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'workshop_id' => $this->workshop->id,
            'workshop_title' => $this->workshop->title,
            'workshop_date' => $this->workshop->start_time->toDateTimeString(),
            'registration_id' => $this->registration?->id,
            'message' => "You have been registered for the workshop: {$this->workshop->title}",
            'type' => 'workshop_registration',
            'action_url' => route('workshops.public.show', $this->workshop->id),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}