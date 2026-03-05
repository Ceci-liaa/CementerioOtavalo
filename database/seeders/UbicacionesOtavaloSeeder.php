<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Canton;
use App\Models\Parroquia;
use App\Models\Comunidad;

class UbicacionesOtavaloSeeder extends Seeder
{
    /**
     * Seeder de ubicaciones geográficas: Cantón Otavalo → Parroquias → Comunidades
     * Los códigos únicos (CA-XX, PA-XX, CO-XX) se generan automáticamente por los modelos.
     */
    public function run(): void
    {
        // ═══════════════════════════════════════════════════
        // NIVEL 1: CANTÓN
        // ═══════════════════════════════════════════════════
        $otavalo = Canton::create(['nombre' => 'Otavalo']);

        $this->command->info("✅ Cantón creado: {$otavalo->nombre} ({$otavalo->codigo})");

        // ═══════════════════════════════════════════════════
        // NIVEL 2 y 3: PARROQUIAS + COMUNIDADES
        // ═══════════════════════════════════════════════════
        $parroquiasData = [
            // ── Parroquias Urbanas ──
            'El Jordán' => [
                'Barrio Machángara',
                'Barrio Cardón Bajo',
                'Barrio La Florida',
                'Barrio Rey Loma',
                'Monserrat',
                'La Banda de Santa Rosa',
                'Kichwa Los Lagos',
            ],
            'San Luis' => [
                'Otavalo Centro',
                'Barrio Punyaro',
                'Barrio Obraje',
                'La Joya',
                'Cotama',
                'Azama',
            ],

            // ── Parroquias Rurales ──
            'Dr. Miguel Egas Cabezas' => [
                'Peguche',
                'Quinchuquí',
                'Agato',
                'Arias Uku',
                'Yakupata',
                'Faccha Llacta',
                'Arias Pamba',
            ],
            'Eugenio Espejo' => [
                'Eugenio Espejo',
                'Calpaquí',
                'Censo Copacabana',
                'Pivarinci',
                'Huacsara',
                'Barrio Wasipungo',
                'La Compañía',
                'Mojandita de Curubí',
                'Mojandita de Avelino Dávila',
            ],
            'San Juan de Ilumán' => [
                'Carabuela',
                'Santiaguillo',
                'San Juan Alto',
                'San Juan Capilla',
                'San Juan Loma',
                'Guanansí',
                'Cachiculla',
                'Minas Chupa',
            ],
            'San José de Quichinche' => [
                'Gualapuro',
                'Rinconada',
                'Yambiro',
                'Uyancha',
            ],
            'San Pablo del Lago' => [
                'Camuendo',
                'Imbabuela',
                'Pucará de Velásquez',
                'Pucará Alto',
                'Pucará Desaguadero',
            ],
            'San Rafael de la Laguna' => [
                'La Bolsa',
                'San Luis de Pigulca',
                'San Luis de Patalanga',
            ],
            'González Suárez' => [],
            'San Pedro de Pataquí' => [],
            'Selva Alegre' => [],
        ];

        $totalParroquias = 0;
        $totalComunidades = 0;

        foreach ($parroquiasData as $nombreParroquia => $comunidades) {
            // Crear parroquia (el código PA-XX se genera automáticamente)
            $parroquia = Parroquia::create([
                'canton_id' => $otavalo->id,
                'nombre' => $nombreParroquia,
            ]);

            $totalParroquias++;
            $this->command->info("  📍 Parroquia: {$parroquia->nombre} ({$parroquia->codigo})");

            // Crear comunidades de esta parroquia
            foreach ($comunidades as $nombreComunidad) {
                $comunidad = Comunidad::create([
                    'parroquia_id' => $parroquia->id,
                    'nombre' => $nombreComunidad,
                ]);

                $totalComunidades++;
                $this->command->info("    🏘️ Comunidad: {$comunidad->nombre} ({$comunidad->codigo_unico})");
            }
        }

        $this->command->info("");
        $this->command->info("══════════════════════════════════════════");
        $this->command->info("✅ Seeder completado:");
        $this->command->info("   Cantón: 1 (Otavalo)");
        $this->command->info("   Parroquias: {$totalParroquias}");
        $this->command->info("   Comunidades: {$totalComunidades}");
        $this->command->info("══════════════════════════════════════════");
    }
}
