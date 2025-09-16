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
        Schema::create('idea_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('idea_id')->constrained('ideas')->onDelete('cascade');
            $table->foreignUlid('user_id')->constrained('users')->onDelete('cascade');
            $table->text('comment');
            $table->boolean('is_supervisor')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('idea_comments')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['idea_id', 'created_at']);
            $table->index(['parent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idea_comments');
    }
};