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
        // Create a pivot table for multiple edition assignments if needed
        if (!Schema::hasTable('user_edition_assignments')) {
            Schema::create('user_edition_assignments', function (Blueprint $table) {
                $table->id();
                $table->ulid('user_id');
                $table->foreignId('edition_id')->constrained()->onDelete('cascade');
                $table->string('role')->default('hackathon_admin'); // Can be extended for other roles
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['user_id', 'edition_id', 'role']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_edition_assignments');
    }
};
