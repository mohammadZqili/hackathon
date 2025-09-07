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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('featured_image_path')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->datetime('published_at')->nullable();
            $table->char('author_id', 26);
            $table->foreign('author_id')->references('id')->on('users')->onDelete('cascade');
            $table->json('tags')->nullable(); // News tags
            $table->integer('views_count')->default(0);
            
            // Social media integration
            $table->boolean('auto_post_twitter')->default(false);
            $table->boolean('posted_to_twitter')->default(false);
            $table->string('twitter_post_id')->nullable();
            $table->timestamp('twitter_posted_at')->nullable();
            
            $table->json('seo_data')->nullable(); // SEO metadata
            $table->timestamps();
            
            $table->index('slug');
            $table->index(['status', 'published_at']);
            $table->index('is_featured');
            $table->index('author_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
