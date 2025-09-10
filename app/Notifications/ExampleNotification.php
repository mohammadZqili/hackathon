<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Helpers\Settings;

class ExampleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = [];
        
        // Check which notification channels are enabled
        if (Settings::emailNotificationsEnabled()) {
            $channels[] = 'mail';
        }
        
        if (Settings::smsNotificationsEnabled()) {
            // Add SMS channel when implemented
            // $channels[] = 'sms';
        }
        
        if (Settings::inAppNotificationsEnabled()) {
            $channels[] = 'database';
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appName = Settings::get('app.name', 'GuacPanel');
        $brandingColors = Settings::getBrandingColors();
        
        return (new MailMessage)
            ->from(Settings::get('mail.from.address'), Settings::get('mail.from.name'))
            ->subject("Notification from {$appName}")
            ->greeting("Hello {$notifiable->name}!")
            ->line($this->data['message'] ?? 'You have a new notification.')
            ->action('View Details', url('/dashboard'))
            ->line("Thank you for using {$appName}!");
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->data['message'] ?? 'You have a new notification.',
            'type' => $this->data['type'] ?? 'info',
            'data' => $this->data,
        ];
    }
}
