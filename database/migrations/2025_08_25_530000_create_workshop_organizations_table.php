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
        Schema::create('workshop_organizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workshop_id');
            $table->foreignId('organization_id');
            $table->enum('role', ['organizer', 'sponsor', 'partner'])->default('organizer');
            $table->timestamps();

            $table->unique(['workshop_id', 'organization_id']);
            $table->index('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop_organizations');
    }
};
