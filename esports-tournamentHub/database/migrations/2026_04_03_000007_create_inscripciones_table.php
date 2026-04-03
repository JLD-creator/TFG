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
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id('id_inscripcion');
            $table->unsignedBigInteger('id_torneo');
            $table->unsignedBigInteger('id_equipo');
            $table->timestamps();

            $table->foreign('id_torneo')->references('id_torneo')->on('torneos')->cascadeOnDelete();
            $table->foreign('id_equipo')->references('id_equipo')->on('equipos')->cascadeOnDelete();
            $table->unique(['id_torneo', 'id_equipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripciones');
    }
};
