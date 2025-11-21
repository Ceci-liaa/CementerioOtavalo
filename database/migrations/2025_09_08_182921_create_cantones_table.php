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
        Schema::create('cantones', function (Blueprint $t) {
            $t->id();
            // 👇 Aquí agregamos el código identificador (ej: 'UIO', 'GYE', '0101')
            $t->string('codigo', 20)->unique()->after('id'); 
            
            $t->string('nombre', 255)->unique();
            $t->timestampsTz();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cantones');
    }
};