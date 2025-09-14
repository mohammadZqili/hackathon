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
        Schema::create('workshop_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id');
            $table->char('user_id', 26);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('barcode', 50)->unique(); // For attendance tracking
            $table->enum('status', ['registered', 'confirmed', 'cancelled', 'attended', 'no_show'])->default('registered');
            $table->timestamp('registered_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('attended_at')->nullable();
            $table->string('attendance_method')->nullable(); // 'barcode_scan', 'manual', etc.
            $table->char('marked_by', 26)->nullable(); // Who marked attendance
            $table->foreign('marked_by')->references('id')->on('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->json('additional_data')->nullable(); // Extra registration data
            $table->timestamps();

            $table->unique(['workshop_id', 'user_id']);
            $table->index('barcode');
            $table->index(['workshop_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_registrations');
    }
};
