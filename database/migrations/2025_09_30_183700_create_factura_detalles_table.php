<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\DB; // solo si vas a usar el CHECK opcional en Postgres

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('factura_detalles', function (Blueprint $t) {
            $t->id();

            // Cabecera: si se elimina la factura, se eliminan sus líneas
            $t->foreignId('factura_id')
              ->constrained('facturas')
              ->cascadeOnDelete();

            // Concepto: puede ser un BENEFICIO o un SERVICIO (uno u otro)
            $t->foreignId('beneficio_id')
              ->nullable()
              ->constrained('beneficios')
              ->nullOnDelete();

            $t->foreignId('servicio_id')
              ->nullable()
              ->constrained('servicios')
              ->nullOnDelete();

            // Snapshot del nombre/descripcion del ítem al momento de facturar
            $t->string('descripcion', 255)->nullable();

            // Detalle de la línea
            $t->integer('cantidad')->default(1);
            $t->decimal('precio', 10, 2);   // precio unitario
            $t->decimal('subtotal', 10, 2); // cantidad * precio

            $t->timestampsTz();

            // Índices útiles
            $t->index(['factura_id']);
            $t->index(['beneficio_id']);
            $t->index(['servicio_id']);
        });

        // === OPCIONAL (solo Postgres): Enforce “al menos uno” de beneficio_id o servicio_id ===
        // DB::statement("
        //     ALTER TABLE factura_detalles
        //     ADD CONSTRAINT chk_factura_detalles_item
        //     CHECK (
        //         (beneficio_id IS NOT NULL AND servicio_id IS NULL)
        //         OR
        //         (beneficio_id IS NULL AND servicio_id IS NOT NULL)
        //     )
        // ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Si agregaste el CHECK opcional en Postgres, primero elimínalo:
        // DB::statement("ALTER TABLE factura_detalles DROP CONSTRAINT IF EXISTS chk_factura_detalles_item");

        Schema::dropIfExists('factura_detalles');
    }
};
