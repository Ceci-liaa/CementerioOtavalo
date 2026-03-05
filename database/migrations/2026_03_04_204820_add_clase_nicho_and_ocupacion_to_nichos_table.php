<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nichos', function (Blueprint $table) {
            if (!Schema::hasColumn('nichos', 'clase_nicho')) {
                $table->string('clase_nicho', 20)->default('BOVEDA')->after('tipo_nicho');
            }
            if (!Schema::hasColumn('nichos', 'ocupacion')) {
                $table->unsignedTinyInteger('ocupacion')->default(0)->after('capacidad');
            }
        });
    }

    public function down(): void
    {
        Schema::table('nichos', function (Blueprint $table) {
            $table->dropColumn(['clase_nicho', 'ocupacion']);
        });
    }
};
