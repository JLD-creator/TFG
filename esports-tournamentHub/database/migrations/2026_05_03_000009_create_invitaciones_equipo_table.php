<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitaciones_equipo', function (Blueprint $table) {
            $table->id('id_invitacion');
            $table->unsignedBigInteger('id_equipo');
            $table->unsignedBigInteger('id_usuario_invitado');
            $table->unsignedBigInteger('id_usuario_invitador');
            $table->string('estado')->default('pendiente');
            $table->timestamps();

            $table->foreign('id_equipo')->references('id_equipo')->on('equipos')->cascadeOnDelete();
            $table->foreign('id_usuario_invitado')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('id_usuario_invitador')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitaciones_equipo');
    }
};
