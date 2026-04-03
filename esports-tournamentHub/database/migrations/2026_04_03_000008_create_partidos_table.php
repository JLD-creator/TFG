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
        Schema::create('partidos', function (Blueprint $table) {
            $table->id('id_partido');
            $table->unsignedBigInteger('id_torneo');
            $table->unsignedBigInteger('id_equipo1');
            $table->unsignedBigInteger('id_equipo2');
            $table->integer('ronda');
            $table->integer('resultado_equipo1')->nullable();
            $table->integer('resultado_equipo2')->nullable();
            $table->unsignedBigInteger('ganador')->nullable();
            $table->timestamps();

            $table->foreign('id_torneo')->references('id_torneo')->on('torneos')->cascadeOnDelete();
            $table->foreign('id_equipo1')->references('id_equipo')->on('equipos')->restrictOnDelete();
            $table->foreign('id_equipo2')->references('id_equipo')->on('equipos')->restrictOnDelete();
            $table->foreign('ganador')->references('id_equipo')->on('equipos')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidos');
    }
};
