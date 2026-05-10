<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo_bajas', function (Blueprint $table) {
            $table->id('id_baja');
            $table->unsignedBigInteger('id_equipo');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamps();

            $table->foreign('id_equipo')->references('id_equipo')->on('equipos')->cascadeOnDelete();
            $table->foreign('id_usuario')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['id_equipo', 'id_usuario']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_bajas');
    }
};
