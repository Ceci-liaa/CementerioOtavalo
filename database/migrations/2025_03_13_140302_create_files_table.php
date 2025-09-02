<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_name_id');
            $table->string('prefix')->nullable();
            $table->string('suffix')->nullable();
            $table->string('name_original'); // Nombre con el que subió el usuario
            $table->string('name_stored');   // Nombre con el que se guardó en el servidor
            $table->string('type', 50);
            $table->unsignedBigInteger('folder_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Relaciones
            $table->foreign('file_name_id')->references('id')->on('file_names')->onDelete('restrict');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
