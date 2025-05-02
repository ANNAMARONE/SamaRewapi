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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('signalement_id')->constrained('signalements')->onDelete('cascade');
            $table->boolean('vote')->default(true); // true = vote pour, false = contre ou neutre selon usage
            $table->timestamps();
        
            $table->unique(['users_id', 'signalement_id']); // un seul vote par users par signalement
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
