<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table): void {
            // ID
            $table->id();

            // Name
            $table->string('name')->unique();

            // Power
            $table->integer('power')->unsigned();

            // Played
            $table->integer('played')->default(0);

            // Points
            $table->integer('points')->default(0);

            // Goals For
            $table->integer('goals_for')->default(0);

            // Goals Against
            $table->integer('goals_against')->default(0);

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
