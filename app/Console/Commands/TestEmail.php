<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Helpers\MailConfig;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email? : The email address to send to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'test@example.com';

        $this->info('Configuring mail settings from database...');
        MailConfig::configure();

        $this->info('Current mail configuration:');
        $this->info('Host: ' . config('mail.mailers.smtp.host'));
        $this->info('Port: ' . config('mail.mailers.smtp.port'));
        $this->info('Username: ' . config('mail.mailers.smtp.username'));
        $this->info('Encryption: ' . config('mail.mailers.smtp.encryption'));
        $this->info('From Address: ' . config('mail.from.address'));
        $this->info('From Name: ' . config('mail.from.name'));

        $this->info("Sending test email to: $email");

        try {
            Mail::raw('This is a test email from GuacPanel. If you receive this, your SMTP settings are working correctly!', function ($message) use ($email) {
                $message->to($email)
                    ->subject('GuacPanel - SMTP Test Email');
            });

            $this->info('✅ Test email sent successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test email: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}