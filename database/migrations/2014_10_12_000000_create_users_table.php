<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre del usuario
            $table->string('email')->unique(); // Email único
            $table->timestamp('email_verified_at')->nullable(); // Verificación de email
            $table->string('password'); // Contraseña encriptada
            $table->string('phone')->nullable(); // Número de teléfono
            $table->string('location')->nullable(); // Ubicación
            $table->text('about')->nullable(); // Descripción personal
            $table->unsignedBigInteger('role_id')->nullable(); // Relación con la tabla roles
            $table->boolean('status')->default(false); // Estado del usuario (activo/inactivo)
            $table->rememberToken(); // Token para recordar sesión
            $table->timestamps(); // Fechas de creación y actualización

            // Añadir campos para bloqueo de la cuenta
            $table->integer('failed_attempts')->default(0); // Intentos fallidos
            $table->timestamp('locked_at')->nullable(); // Fecha de bloqueo

            // 🔹 Clave foránea para role_id, enlazada a roles.id
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            
            // 🔹 Nuevo campo para código único
            $table->string('codigo_usuario')->nullable()->unique(); // Código único
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
