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
        Schema::create('editions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('year');
            $table->date('registration_start_date');
            $table->date('registration_end_date');
            $table->date('hackathon_start_date');
            $table->date('hackathon_end_date');
            $table->ulid('admin_id')->nullable();
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->integer('max_teams')->default(100);
            $table->integer('max_team_members')->default(5);
            $table->boolean('is_active')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};