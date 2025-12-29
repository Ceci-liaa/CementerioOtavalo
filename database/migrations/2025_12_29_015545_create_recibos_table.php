<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. TABLA PADRE: RECIBOS (La Factura)
        Schema::create('recibos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('socio_id')->constrained('socios')->cascadeOnDelete();
            $table->date('fecha_pago');
            $table->decimal('total', 10, 2); // La suma total pagada
            $table->string('observacion')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // 2. MODIFICAR TABLA HIJA: PAGOS
        // Le agregamos la columna 'recibo_id' para saber a qué factura pertenece
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreignId('recibo_id')->nullable()->constrained('recibos')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['recibo_id']);
            $table->dropColumn('recibo_id');
        });
        Schema::dropIfExists('recibos');
    }

};
