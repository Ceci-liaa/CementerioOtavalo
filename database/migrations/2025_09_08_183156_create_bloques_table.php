<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Asegurar que PostGIS esté activo
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        Schema::create('bloques', function (Blueprint $t) {
            $t->id();
            // Código autogenerado (p. ej. B0001, B0002)
            $t->string('codigo', 10)->unique();
            $t->string('nombre', 255);
            $t->text('descripcion')->nullable();
            // Relación con la capa geométrica (tabla bloques_geom)
            $t->foreignId('bloque_geom_id')
                ->nullable()
                ->constrained('bloques_geom')
                ->nullOnDelete();
            $t->decimal('area_m2', 12, 2)->nullable(); // área calculada o manual
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();
        });

        // Columna geométrica para el polígono del bloque (PostGIS)
        DB::statement("ALTER TABLE bloques ADD COLUMN geom geometry(POLYGON, 4326)");
        DB::statement("CREATE INDEX bloques_geom_gix ON bloques USING GIST (geom)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloques');
    }
};
