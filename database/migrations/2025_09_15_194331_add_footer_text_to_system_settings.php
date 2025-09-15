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
        // Check if footer_text setting doesn't exist, then add it
        if (!DB::table('system_settings')->where('key', 'footer_text')->exists()) {
            DB::table('system_settings')->insert([
                'key' => 'footer_text',
                'value' => '',
                'group' => 'branding',
                'type' => 'string',
                'description' => 'Footer text displayed at the bottom of the site',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->where('key', 'footer_text')->delete();
    }
};