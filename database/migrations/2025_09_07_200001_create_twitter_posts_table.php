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
        Schema::create('twitter_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id');
            $table->string('tweet_id', 100)->nullable();
            $table->text('tweet_content');
            $table->json('tweet_metadata')->nullable(); // likes, retweets, etc
            $table->enum('status', ['pending', 'posted', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->index(['news_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twitter_posts');
    }
};
