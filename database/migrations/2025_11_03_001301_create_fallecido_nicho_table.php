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
            // -------------------

            $t->foreignId('fallecido_id')->constrained('fallecidos')->cascadeOnDelete();
            $t->foreignId('nicho_id')->constrained('nichos')->restrictOnDelete();
            $t->unsignedInteger('posicion')->default(1); 
            $t->date('fecha_inhumacion')->nullable();
            $t->date('fecha_exhumacion')->nullable();
            $t->text('observacion')->nullable();
            $t->timestampsTz();
            
            $t->unique(['nicho_id', 'posicion']); 
            $t->index(['fallecido_id', 'nicho_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fallecido_nicho');
    }
};