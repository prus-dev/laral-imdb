<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('watchlist_items', function (Blueprint $table): void {
            $table->id();
            $table->string('imdb_id')->unique();
            $table->string('title');
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('poster_url')->nullable();
            $table->decimal('rating', 3, 1)->nullable();
            $table->unsignedInteger('runtime_seconds')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlist_items');
    }
};
