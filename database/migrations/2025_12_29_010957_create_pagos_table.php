<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('pagos', function (Blueprint $table) {
        $table->id();

        // 1. RELACIÓN CON SOCIO (Obligatorio)
        // Si borras al socio, se borran sus pagos (cascadeOnDelete)
        $table->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();

        // 2. DATOS DEL PAGO
        $table->integer('anio_pagado');       // Ej: 2023, 2024
        $table->decimal('monto', 10, 2);      // Ej: 25.00
        $table->date('fecha_pago');           // Fecha real del cobro
        $table->string('observacion')->nullable(); // Nota opcional

        // 3. AUDITORÍA (Quién cobró)
        $table->foreignId('created_by')->nullable()->constrained('users');
        
        $table->timestamps(); // Crea created_at y updated_at

        // 4. CANDADO DE SEGURIDAD
        // Esto impide que ingreses dos veces el año 2024 al mismo socio por error en la BD.
        $table->unique(['socio_id', 'anio_pagado']);
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
