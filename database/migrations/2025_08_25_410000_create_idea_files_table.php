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
        Schema::create('idea_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_id');
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->string('mime_type');
            $table->bigInteger('file_size'); // in bytes
            $table->enum('file_category', ['presentation', 'document', 'image', 'video', 'other'])->default('document');
            $table->text('description')->nullable();
            $table->char('uploaded_by', 26);
            $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('is_virus_scanned')->default(false);
            $table->boolean('is_safe')->default(true);
            $table->timestamps();

            $table->index(['idea_id', 'file_category']);
            $table->index('uploaded_by');
            $table->index('is_virus_scanned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idea_files');
    }
};
