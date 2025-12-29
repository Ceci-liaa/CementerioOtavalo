<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('socios', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 20)->unique()->nullable()->after('id');
            $t->foreignId('comunidad_id')->nullable()->constrained('comunidades')->nullOnDelete();
            // --- NUEVO CAMPO: Tipo de Beneficio ---
            // Usamos enum para controlar las opciones fijas
            $t->enum('tipo_beneficio', ['sin_subsidio', 'con_subsidio', 'exonerado'])
              ->default('sin_subsidio');
            // --- NUEVO CAMPO: Fecha de Exoneración ---
            $t->date('fecha_exoneracion')->nullable();
            $t->foreignId('genero_id')->nullable()->constrained('generos')->nullOnDelete();
            $t->foreignId('estado_civil_id')->nullable()->constrained('estados_civiles')->nullOnDelete();
            $t->string('cedula', 20)->unique();
            $t->string('nombres', 255);
            $t->string('apellidos', 255);
            $t->string('telefono', 30)->nullable();
            $t->string('direccion', 255)->nullable();
            $t->string('email', 255)->nullable();
            $t->date('fecha_nac')->nullable();
            // --- NUEVO CAMPO: Fecha de Inscripción ---
            // Importante para calcular antigüedad y deudas
            $t->date('fecha_inscripcion')->nullable();
            $t->boolean('es_representante')->default(false);
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();

            $t->index(['comunidad_id', 'apellidos']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('socios');
    }
};