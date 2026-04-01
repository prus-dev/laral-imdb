<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('title_votes', function (Blueprint $table) {
            $table->id();
            $table->string('title_id')->index();
            $table->unsignedTinyInteger('score');
            $table->string('country_code', 2)->nullable()->index();
            $table->string('device_hash')->nullable()->index();
            $table->string('account_hash')->nullable()->index();
            $table->string('ip_hash')->nullable()->index();
            $table->boolean('is_suspicious')->default(false)->index();
            $table->json('suspicious_reasons')->nullable();
            $table->timestamp('voted_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('title_votes');
    }
};
