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
        Schema::create('fallecido_nicho', function (Blueprint $t) {
            $t->id();
            
            // --- CAMPO NUEVO ---
            $t->string('codigo', 20)->unique(); // Ej: ASG-00001

            // Relación con Fallecidos
            $t->foreignId('fallecido_id')->constrained('fallecidos')->cascadeOnDelete();

            // --- AQUÍ INTEGRAMOS EL SOCIO_ID ---
            // Esto reemplaza tu segunda migración completa:
            $t->foreignId('socio_id')
              ->nullable()              // Puede ser nulo
              ->constrained('socios')   // Se conecta a la tabla 'socios'
              ->nullOnDelete();         // Si se borra el socio, aquí se pone NULL (set null)
            // -----------------------------------

            $t->foreignId('nicho_id')->constrained('nichos')->restrictOnDelete();
            
            $t->unsignedInteger('posicion')->default(1); 
            $t->date('fecha_inhumacion')->nullable();
            $t->date('fecha_exhumacion')->nullable();
            $t->text('observacion')->nullable();
            
            // Supabase (PostgreSQL) maneja muy bien timestampsTz
            $t->timestampsTz();
            
            // Indices y claves únicas compuestas
            $t->unique(['nicho_id', 'posicion']); 
            $t->index(['fallecido_id', 'nicho_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fallecido_nicho');
    }
};