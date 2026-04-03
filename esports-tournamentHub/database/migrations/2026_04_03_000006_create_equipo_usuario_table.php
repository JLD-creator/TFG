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
        Schema::create('equipo_usuario', function (Blueprint $table) {
            $table->unsignedBigInteger('id_equipo');
            $table->unsignedBigInteger('id_usuario');

            $table->primary(['id_equipo', 'id_usuario']);
            $table->foreign('id_equipo')->references('id_equipo')->on('equipos')->cascadeOnDelete();
            $table->foreign('id_usuario')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipo_usuario');
    }
};
