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
        Schema::create('facturas', function (Blueprint $t) {
            $t->id();

            // Relación opcional con socio
            $t->foreignId('socio_id')->nullable()->constrained('socios')->nullOnDelete();

            // Datos snapshot del cliente (para socios o externos)
            $t->string('cliente_nombre', 255);
            $t->string('cliente_apellido', 255)->nullable();
            $t->string('cliente_cedula', 20)->nullable();
            $t->string('cliente_email', 255)->nullable();
            $t->string('cliente_telefono', 30)->nullable();

            // Info general de la factura
            $t->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $t->decimal('total', 10, 2)->default(0);
            // Estados: PENDIENTE, EMITIDA, PAGADA, ANULADA
            $t->string('estado', 20)->default('PENDIENTE');

            // ===== Auditoría / Trazabilidad =====
            // Quién/cuándo la emitió (cierra y ya no se edita)
            $t->timestampTz('emitida_at')->nullable();
            $t->foreignId('emitida_por')->nullable()->constrained('users')->nullOnDelete();

            // Quién/cuándo la anuló y por qué (no se borra)
            $t->timestampTz('anulada_at')->nullable();
            $t->foreignId('anulada_por')->nullable()->constrained('users')->nullOnDelete();
            $t->string('motivo_anulacion', 500)->nullable();

            $t->timestampsTz();

            // Índices útiles para listados/consultas
            $t->index(['estado', 'fecha']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
