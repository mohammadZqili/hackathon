<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class MailtrapSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set SMTP settings from .env values for Mailtrap
        SystemSetting::set('mail_host', env('MAIL_HOST', 'sandbox.smtp.mailtrap.io'), 'smtp');
        SystemSetting::set('mail_port', env('MAIL_PORT', 2525), 'smtp');
        SystemSetting::set('mail_username', env('MAIL_USERNAME', '50b9d114179041'), 'smtp');
        SystemSetting::set('mail_password', env('MAIL_PASSWORD', '8061d5bd998f91'), 'smtp');
        SystemSetting::set('mail_encryption', env('MAIL_ENCRYPTION', 'tls'), 'smtp');
        SystemSetting::set('mail_from_address', env('MAIL_FROM_ADDRESS', 'admin@guacpanel.test'), 'smtp');
        SystemSetting::set('mail_from_name', env('MAIL_FROM_NAME', 'GuacPanel Admin'), 'smtp');

        $this->command->info('Mailtrap SMTP settings have been configured in database.');
    }
}