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
        Schema::create('fixtures', function (Blueprint $table): void {
            // ID
            $table->id();

            // Home Team ID
            $table->foreignId('home_team_id')
                ->constrained('teams')
                ->onDelete('cascade');

            // Away Team ID
            $table->foreignId('away_team_id')
                ->constrained('teams')
                ->onDelete('cascade');

            // Week
            $table->unsignedInteger('week');

            // Home Score
            $table->unsignedInteger('home_score')->default(0);

            // Away Score
            $table->unsignedInteger('away_score')->default(0);

            // Played
            $table->boolean('played')->default(false);

            // Timestamps
            $table->timestamps();

            // Unique Fixture Index
            $table->unique(['home_team_id', 'away_team_id'], 'unique_fixture_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
