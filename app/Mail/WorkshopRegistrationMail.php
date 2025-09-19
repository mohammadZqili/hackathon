<?php

namespace App\Mail;

use App\Models\Workshop;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkshopRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Workshop $workshop;
    public User $user;
    public string $qrCode;
    public ?int $registrationId;

    /**
     * Create a new message instance.
     */
    public function __construct(Workshop $workshop, User $user, ?int $registrationId = null)
    {
        $this->workshop = $workshop;
        $this->user = $user;
        $this->registrationId = $registrationId;

        // Generate QR code for this user and workshop
        // QR contains workshop name and user email for tracking attendance
        $qrService = new QrCodeService();
        $qrContent = json_encode([
            'workshop_id' => $workshop->id,
            'workshop_name' => $workshop->title,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'registration_id' => $registrationId,
            'registered_at' => now()->toDateTimeString()
        ]);

        $this->qrCode = $qrService->generateQrCodeImage($qrContent);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->user->email,
            subject: 'Workshop Registration: ' . $this->workshop->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.workshop-registration',
            with: [
                'workshop' => $this->workshop,
                'user' => $this->user,
                'qrCode' => $this->qrCode,
                'registrationId' => $this->registrationId,
                'workshopDate' => $this->workshop->start_time->format('l, F j, Y'),
                'workshopTime' => $this->workshop->start_time->format('g:i A') . ' - ' . $this->workshop->end_time->format('g:i A'),
                'location' => $this->workshop->location ?? 'Online',
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}