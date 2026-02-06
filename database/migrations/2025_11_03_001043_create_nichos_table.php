<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nichos', function (Blueprint $t) {
            $t->id();
            
            // 1. RELACIONES (Incluyendo la de socio que querías unificar)
            $t->foreignId('socio_id')
              ->nullable()
              ->constrained('socios')
              ->nullOnDelete(); 

            $t->foreignId('bloque_id')
              ->constrained('bloques')
              ->cascadeOnUpdate()
              ->restrictOnDelete();

            $t->foreignId('nicho_geom_id')
              ->nullable()
              ->constrained('nichos_geom') 
              ->nullOnDelete();

            // 2. IDENTIFICACIÓN
            $t->string('codigo', 10)->unique(); // Ej: N001
            $t->uuid('qr_uuid')->nullable()->unique();


            // 3. CLASIFICACIÓN (Tus nombres originales)
            
            // tipo_nicho: PROPIO o COMPARTIDO
            $t->enum('tipo_nicho', ['PROPIO', 'COMPARTIDO'])->default('PROPIO');
            
            // clase_nicho: (NUEVO) BOVEDA o TIERRA
            $t->enum('clase_nicho', ['BOVEDA', 'TIERRA'])->default('BOVEDA');


            // 4. CAPACIDAD Y OCUPACIÓN
            
            // capacidad: (Tu campo original) Default 3 difuntos.
            $t->unsignedTinyInteger('capacidad')->default(3);
            
            // ocupacion: (NUEVO) Contador para saber cuántos de los 3 están usados (0, 1, 2 o 3).
            $t->unsignedTinyInteger('ocupacion')->default(0); 

            // disponible: (Tu campo original) 
            // true = Disponible (Vacío o con espacio).
            // false = No disponible (Ocupado/Lleno).
            $t->boolean('disponible')->default(true);


            // 5. ESTADO FÍSICO
            // estado: (Tu campo original) Lo usamos solo para mantenimiento físico.
            $t->enum('estado', ['BUENO', 'MANTENIMIENTO', 'MALO', 'ABANDONADO'])
              ->default('BUENO');
            
            // 6. EXTRAS
            $t->text('descripcion')->nullable(); 
            
            // Auditoría
            $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $t->timestampsTz();
            $t->softDeletesTz();

            // Índices sugeridos
            $t->index(['estado', 'disponible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nichos');
    }
};