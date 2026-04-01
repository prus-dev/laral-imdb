<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->string('movie_imdb_id', 50)->index();
            $table->string('movie_title')->nullable();
            $table->string('author_name', 120)->nullable();
            $table->string('user_fingerprint', 64)->index();
            $table->text('content');
            $table->string('status', 20)->default('approved')->index();
            $table->json('flags')->nullable();
            $table->unsignedTinyInteger('quality_score')->default(0);
            $table->boolean('is_ai_spam')->default(false);
            $table->timestamps();

            $table->unique(['movie_imdb_id', 'user_fingerprint', 'content'], 'reviews_unique_per_user_content');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
