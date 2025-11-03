<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Asegurar la extensión PostGIS
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        // Crear la tabla sin la columna geom (la añadimos con SQL para evitar problemas de sintaxis)
        Schema::create('bloques_geom', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->nullable();
            $table->timestamps();
        });

        // Añadir columna geom correctamente (MULTIPOLYGON, SRID 4326) y crear índice GIST
        // Ajusta MULTIPOLYGON a POLYGON si prefieres solo POLYGON
        DB::statement("ALTER TABLE bloques_geom ADD COLUMN geom geometry(MULTIPOLYGON, 4326)");
        DB::statement("CREATE INDEX IF NOT EXISTS bloques_geom_gix ON bloques_geom USING GIST (geom)");
    }

    public function down(): void
    {
        // Eliminar índice si existe y luego la tabla
        DB::statement('DROP INDEX IF EXISTS bloques_geom_gix');
        Schema::dropIfExists('bloques_geom');
    }
};
