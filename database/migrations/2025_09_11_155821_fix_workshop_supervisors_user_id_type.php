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
        Schema::table('workshop_supervisors', function (Blueprint $table) {
            // Change user_id from bigint to string to match users table ULID
            $table->string('user_id', 26)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workshop_supervisors', function (Blueprint $table) {
            // Revert back to bigint
            $table->unsignedBigInteger('user_id')->change();
        });
    }
};
