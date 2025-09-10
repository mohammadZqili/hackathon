<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->nullable()->index(); // smtp, sms, branding, notifications, etc.
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $defaultSettings = [
            // SMTP Settings
            ['key' => 'mail_host', 'value' => '', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_port', 'value' => '587', 'group' => 'smtp', 'type' => 'integer'],
            ['key' => 'mail_username', 'value' => '', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_password', 'value' => '', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_encryption', 'value' => 'tls', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_from_address', 'value' => '', 'group' => 'smtp', 'type' => 'string'],
            ['key' => 'mail_from_name', 'value' => 'GuacPanel', 'group' => 'smtp', 'type' => 'string'],
            
            // SMS Settings
            ['key' => 'sms_provider', 'value' => 'twilio', 'group' => 'sms', 'type' => 'string'],
            ['key' => 'sms_api_key', 'value' => '', 'group' => 'sms', 'type' => 'string'],
            ['key' => 'sms_api_secret', 'value' => '', 'group' => 'sms', 'type' => 'string'],
            ['key' => 'sms_from_number', 'value' => '', 'group' => 'sms', 'type' => 'string'],
            
            // Branding Settings
            ['key' => 'app_name', 'value' => 'GuacPanel', 'group' => 'branding', 'type' => 'string'],
            ['key' => 'app_logo', 'value' => '', 'group' => 'branding', 'type' => 'string'],
            ['key' => 'app_favicon', 'value' => '', 'group' => 'branding', 'type' => 'string'],
            ['key' => 'primary_color', 'value' => '#0d9488', 'group' => 'branding', 'type' => 'string'],
            ['key' => 'secondary_color', 'value' => '#14b8a6', 'group' => 'branding', 'type' => 'string'],
            ['key' => 'success_color', 'value' => '#10b981', 'group' => 'branding', 'type' => 'string'],
            ['key' => 'danger_color', 'value' => '#ef4444', 'group' => 'branding', 'type' => 'string'],
            
            // Twitter Settings
            ['key' => 'twitter_api_key', 'value' => '', 'group' => 'twitter', 'type' => 'string'],
            ['key' => 'twitter_api_secret', 'value' => '', 'group' => 'twitter', 'type' => 'string'],
            ['key' => 'twitter_access_token', 'value' => '', 'group' => 'twitter', 'type' => 'string'],
            ['key' => 'twitter_access_token_secret', 'value' => '', 'group' => 'twitter', 'type' => 'string'],
            ['key' => 'twitter_auto_post', 'value' => '0', 'group' => 'twitter', 'type' => 'boolean'],
            
            // Notification Settings
            ['key' => 'notification_email_enabled', 'value' => '1', 'group' => 'notifications', 'type' => 'boolean'],
            ['key' => 'notification_sms_enabled', 'value' => '0', 'group' => 'notifications', 'type' => 'boolean'],
            ['key' => 'notification_push_enabled', 'value' => '0', 'group' => 'notifications', 'type' => 'boolean'],
            ['key' => 'notification_in_app_enabled', 'value' => '1', 'group' => 'notifications', 'type' => 'boolean'],
        ];

        foreach ($defaultSettings as $setting) {
            DB::table('system_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
