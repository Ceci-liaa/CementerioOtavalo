<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 255);
            $t->text('descripcion')->nullable();
            $t->decimal('valor', 10, 2)->nullable(); // precio sugerido
            $t->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
    }
};
