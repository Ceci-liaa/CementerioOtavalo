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

        // Crear la tabla bloques (incluye la FK hacia bloques_geom porque ya existe)
        Schema::create('bloques', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 10)->unique();
            $t->string('nombre', 255);
            $t->text('descripcion')->nullable();

            // FK hacia bloques_geom (asegúrate de que bloques_geom ya exista)
            $t->foreignId('bloque_geom_id')
                ->nullable()
                ->constrained('bloques_geom')
                ->nullOnDelete();

            $t->decimal('area_m2', 12, 2)->nullable();
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();
        });

        // Agregar la columna geom con PostGIS y crear índice GIST con nombre único
        DB::statement("ALTER TABLE bloques ADD COLUMN IF NOT EXISTS geom geometry(MULTIPOLYGON, 4326)");
        DB::statement("CREATE INDEX IF NOT EXISTS bloques_gix ON bloques USING GIST (geom)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Si la tabla existe, eliminar la FK primero (si existe) para evitar errores al dropear la tabla
        if (Schema::hasTable('bloques')) {
            Schema::table('bloques', function (Blueprint $table) {
                // Intentamos dropear la FK; si no existe lanzará excepción en algunos entornos,
                // pero en la mayoría de setups la FK sí existe y se eliminará correctamente.
                // Si por seguridad quieres evitar excepciones, puedes envolver esto en try/catch.
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                // Simplemente intentar dropear la FK por columna (funciona en la mayoría de casos)
                // Si la FK no existe el método puede lanzar excepción; si te da problemas,
                // podemos reemplazar por una instrucción SQL DROP CONSTRAINT IF EXISTS con el nombre exacto.
                try {
                    $table->dropForeign(['bloque_geom_id']);
                } catch (\Throwable $e) {
                    // Silenciar: si no existe la FK, continuamos con la limpieza
                }
            });

            // Eliminar índice espacial si existe
            DB::statement('DROP INDEX IF EXISTS bloques_gix');
        }

        // Finalmente eliminar la tabla
        Schema::dropIfExists('bloques');
    }
};
