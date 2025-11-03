<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Activar la extensión PostGIS si no existe
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create('bloques_geom', function (Blueprint $table) {
            $table->id(); // id serial (PK)
            $table->string('nombre', 100)->nullable(); // nombre del bloque (Bloque A, B, etc.)
            // Columna geométrica tipo MULTIPOLYGON con SRID 4326
            $table->geometry('geom', 4326);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloques_geom');
    }
};
