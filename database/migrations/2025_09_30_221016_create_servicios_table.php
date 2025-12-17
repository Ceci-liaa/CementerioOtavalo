<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $t) {
            $t->id();
            // Código único, ej: S001. Indexado para búsquedas rápidas.
            $t->string('codigo', 20)->unique()->after('id'); 
            $t->string('nombre', 255);
            $t->text('descripcion')->nullable();
            $t->decimal('valor', 10, 2)->nullable();
            $t->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};