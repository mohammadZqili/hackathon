<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Team;
use App\Models\TeamInvitation as TeamInvitationModel;
use App\Helpers\Settings;

class TeamInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $team;
    public $invitation;
    public $registrationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Team $team, TeamInvitationModel $invitation)
    {
        $this->team = $team->load(['edition', 'leader']);
        $this->invitation = $invitation;

        // Build registration URL with invitation token
        $this->registrationUrl = url('/register') . '?' . http_build_query([
            'invitation' => $invitation->token,
            'email' => $invitation->email
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $hackathonName = $this->team->edition->name ?? 'Hackathon';
        return new Envelope(
            subject: "You're invited to join Team '{$this->team->name}' for {$hackathonName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Get app name from settings or fallback to config
        $appName = Settings::get('app.name') ?? config('app.name', 'GuacPanel');

        // Get hackathon/edition information
        $hackathonName = $this->team->edition->name ?? 'Hackathon';
        $hackathonYear = $this->team->edition->year ?? date('Y');

        return new Content(
            view: 'emails.team-invitation-link',
            with: [
                'appName' => $appName,
                'teamName' => $this->team->name,
                'teamDescription' => $this->team->description,
                'teamLeaderName' => $this->team->leader->name ?? 'Team Leader',
                'hackathonName' => $hackathonName,
                'hackathonYear' => $hackathonYear,
                'editionName' => $this->team->edition->name ?? null,
                'registrationUrl' => $this->registrationUrl,
                'expiresAt' => $this->invitation->expires_at,
                'email' => $this->invitation->email,
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