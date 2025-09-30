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
            $t->string('estado', 20)->default('PENDIENTE'); // PENDIENTE, PAGADA, ANULADA

            $t->timestampsTz();
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
