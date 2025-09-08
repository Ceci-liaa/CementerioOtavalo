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
        Schema::create('parroquias', function (Blueprint $t) {
            $t->id();
            $t->foreignId('canton_id')->constrained('cantones')->cascadeOnUpdate()->restrictOnDelete();
            $t->string('nombre', 255);
            $t->timestampsTz();
            $t->unique(['canton_id', 'nombre']); // no duplicar por cantón
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('parroquias');
    }
};
