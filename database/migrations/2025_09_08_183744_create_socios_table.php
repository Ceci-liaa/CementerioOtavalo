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
            $t->foreignId('comunidad_id')->constrained('comunidades'); 
            // Enum para tipo de beneficio (ya estaba, es obligatorio por defecto al tener default)
            $t->enum('tipo_beneficio', ['sin_subsidio', 'con_subsidio', 'exonerado'])
              ->default('sin_subsidio');
            $t->date('fecha_exoneracion')->nullable();
            $t->foreignId('genero_id')->nullable()->constrained('generos')->nullOnDelete();
            $t->foreignId('estado_civil_id')->constrained('estados_civiles');
            $t->enum('condicion', ['ninguna', 'discapacidad', 'enfermedad_terminal'])->default('ninguna');
            $t->enum('estatus', ['vivo', 'fallecido'])->default('vivo');
            $t->string('cedula', 20)->unique();
            $t->string('nombres', 255);
            $t->string('apellidos', 255);
            $t->string('telefono', 30)->nullable();
            $t->string('direccion', 255)->nullable();
            $t->string('email', 255)->nullable();
            $t->date('fecha_nac')->nullable();
            $t->date('fecha_inscripcion')->nullable();
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