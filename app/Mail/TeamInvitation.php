<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Team;
use App\Helpers\Settings;

class TeamInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $team;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, Team $team)
    {
        $this->user = $user;
        $this->team = $team->load(['edition', 'hackathon']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $hackathonName = $this->team->edition->name ?? 'Hackathon';
        return new Envelope(
            subject: $hackathonName . ' - Invitation to Join Team: ' . $this->team->name,
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
            view: 'emails.team-invitation',
            with: [
                'appName' => $appName,
                'userName' => $this->user->name,
                'teamName' => $this->team->name,
                'teamDescription' => $this->team->description,
                'hackathonName' => $hackathonName,
                'hackathonYear' => $hackathonYear,
                'editionName' => $this->team->edition->name ?? null,
                'loginUrl' => route('login'),
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