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
        Schema::create('workshop_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 50)->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 20);
            $table->string('national_id', 20);
            $table->string('job_title')->nullable();
            $table->enum('job_type', ['student', 'employee'])->default('student');
            $table->foreignId('workshop_id')->constrained('workshops')->onDelete('cascade');
            $table->boolean('attended')->default(false);
            $table->timestamp('registered_at');
            $table->timestamp('attended_at')->nullable();
            $table->char('attended_by', 26)->nullable(); // Who scanned the QR
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('attended_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['workshop_id', 'attended']);
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_attendances');
    }
};