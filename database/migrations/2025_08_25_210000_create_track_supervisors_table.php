<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('track_supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id');
            $table->char('user_id', 26);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['track_id', 'user_id']);
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_supervisors');
    }
};
