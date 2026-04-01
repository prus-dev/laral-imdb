<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table): void {
            $table->id();
            $table->string('user_key', 64)->index();
            $table->string('imdb_id');
            $table->enum('context_type', ['title', 'series', 'episode']);
            $table->unsignedTinyInteger('score');
            $table->timestamp('watched_at')->nullable();
            $table->timestamps();

            $table->unique(['user_key', 'imdb_id', 'context_type']);
            $table->index(['imdb_id', 'context_type']);
        });

        Schema::create('rating_events', function (Blueprint $table): void {
            $table->id();
            $table->string('user_key', 64)->index();
            $table->string('imdb_id');
            $table->enum('context_type', ['title', 'series', 'episode']);
            $table->enum('action', ['rated', 'rerated', 'removed']);
            $table->unsignedTinyInteger('old_score')->nullable();
            $table->unsignedTinyInteger('new_score')->nullable();
            $table->timestamps();

            $table->index(['imdb_id', 'context_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_events');
        Schema::dropIfExists('ratings');
    }
};
