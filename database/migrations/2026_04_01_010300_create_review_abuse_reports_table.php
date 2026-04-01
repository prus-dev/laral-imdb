<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('review_abuse_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('review_id')->constrained()->cascadeOnDelete();
            $table->string('session_key');
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['review_id', 'session_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_abuse_reports');
    }
};
