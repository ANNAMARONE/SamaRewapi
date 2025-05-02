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
        Schema::create('resolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signalement_id')->constrained('signalements')->onDelete('cascade');
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade'); // Autorité qui propose la résolution
            $table->text('proposition');
            $table->timestamp('date_resolution')->nullable();
            $table->enum('statut', ['proposee', 'en_cours', 'terminee'])->default('proposee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resolutions');
    }
};
