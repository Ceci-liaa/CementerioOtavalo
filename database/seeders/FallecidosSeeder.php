<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fallecido;

class FallecidosSeeder extends Seeder
{
    /**
     * Seeder de Fallecidos del cementerio.
     * Los códigos únicos (FA-01, FA-02...) se generan automáticamente por el modelo.
     */
    public function run(): void
    {
        $fallecidos = [
            // ═══════════════════════════════════════
            // --- SEPTIEMBRE 2023 ---
            // ═══════════════════════════════════════
            [
                'comunidad_id' => 15,
                'genero_id' => 1,
                'estado_civil_id' => 1,
                'cedula' => '1051208252',
                'apellidos' => 'REMACHE MORALES',
                'nombres' => 'ELKIN SAID',
                'fecha_nac' => '2023-08-29',
                'fecha_fallecimiento' => '2023-08-29',
                'observaciones' => 'ANEMIA HEMOLITICA AGUDA',
            ],
            [
                'comunidad_id' => 1,
                'genero_id' => 1,
                'estado_civil_id' => 2,
                'cedula' => '1000572907',
                'apellidos' => 'MALDONADO CHIZA',
                'nombres' => 'RAFAEL',
                'fecha_nac' => '1942-09-01',
                'fecha_fallecimiento' => '2023-09-01',
                'observaciones' => 'DIABETES MELLITUS TIPOZ',
            ],
            [
                'comunidad_id' => 20,
                'genero_id' => 1,
                'estado_civil_id' => 4,
                'cedula' => '1001038049',
                'apellidos' => 'OTAVALO GUALACATA',
                'nombres' => 'JOSE MANUEL',
                'fecha_nac' => '1931-09-01',
                'fecha_fallecimiento' => '2023-09-01',
                'observaciones' => 'PARO CARDIACO',
            ],
            [
                'comunidad_id' => 12,
                'genero_id' => 2,
                'estado_civil_id' => 4,
                'cedula' => '1001381480',
                'apellidos' => 'TERAN MUENALA',
                'nombres' => 'MARIA ANGELA',
                'fecha_nac' => '1927-09-02',
                'fecha_fallecimiento' => '2023-09-02',
                'observaciones' => 'PARO CARDIACO',
            ],
            [
                'comunidad_id' => 25,
                'genero_id' => 2,
                'estado_civil_id' => 2,
                'cedula' => '1002137410',
                'apellidos' => 'VASQUES SARANSIG',
                'nombres' => 'MARIA LUCILA',
                'fecha_nac' => '1972-09-04',
                'fecha_fallecimiento' => '2023-09-04',
                'observaciones' => 'EPILEPSIA PARO RESPIRATORIO',
            ],

            // ═══════════════════════════════════════
            // --- OCTUBRE 2023 ---
            // ═══════════════════════════════════════
            [
                'comunidad_id' => 13,
                'genero_id' => 1,
                'estado_civil_id' => 1,
                'cedula' => '1051200457',
                'apellidos' => 'MORALES FUERES',
                'nombres' => 'AXEL MATIAS',
                'fecha_nac' => '2023-10-01',
                'fecha_fallecimiento' => '2023-10-01',
                'observaciones' => 'Sin observaciones registradas',
            ],
            [
                'comunidad_id' => 30,
                'genero_id' => 2,
                'estado_civil_id' => 4,
                'cedula' => '1050126372',
                'apellidos' => 'PERUGACHI GUALSAQUI',
                'nombres' => 'MARIA',
                'fecha_nac' => '1938-10-05',
                'fecha_fallecimiento' => '2023-10-05',
                'observaciones' => 'POLIARTROSIS ESCOLIOSIS MUERTE SIN ASISTENCIA',
            ],
            [
                'comunidad_id' => 2,
                'genero_id' => 2,
                'estado_civil_id' => 1,
                'cedula' => '1051137345',
                'apellidos' => 'TONTAQUIMBA CAHUASQUI',
                'nombres' => 'SARA NICOL',
                'fecha_nac' => '2023-10-08',
                'fecha_fallecimiento' => '2023-10-08',
                'observaciones' => 'PARO CARDIORESPIRATORIO',
            ],
            [
                'comunidad_id' => 14,
                'genero_id' => 2,
                'estado_civil_id' => 2,
                'cedula' => '1002506713',
                'apellidos' => 'CAMUENDO AJALA',
                'nombres' => 'GERTRUDYS',
                'fecha_nac' => '1974-10-12',
                'fecha_fallecimiento' => '2023-10-12',
                'observaciones' => 'CANCER METASTICO',
            ],
            [
                'comunidad_id' => 28,
                'genero_id' => 2,
                'estado_civil_id' => 2,
                'cedula' => '1002500476',
                'apellidos' => 'COLIMBA TITUAÑA',
                'nombres' => 'MARIA XIMENA',
                'fecha_nac' => '1977-10-14',
                'fecha_fallecimiento' => '2023-10-14',
                'observaciones' => 'FALLA MULTIORGANICA',
            ],

            // ═══════════════════════════════════════
            // --- NOVIEMBRE 2023 ---
            // ═══════════════════════════════════════
            [
                'comunidad_id' => 9,
                'genero_id' => 2,
                'estado_civil_id' => 4,
                'cedula' => '1000749398',
                'apellidos' => 'CACHIGUANGO AMAGUAÑA',
                'nombres' => 'MARIA DOLORES',
                'fecha_nac' => '1930-11-02',
                'fecha_fallecimiento' => '2023-11-02',
                'observaciones' => 'TROMBOSIS INTRACARDIACA',
            ],
            [
                'comunidad_id' => 1,
                'genero_id' => 1,
                'estado_civil_id' => 2,
                'cedula' => '1002499950',
                'apellidos' => 'TERAN CEPEDA',
                'nombres' => 'GERMAN',
                'fecha_nac' => '1977-11-05',
                'fecha_fallecimiento' => '2023-11-05',
                'observaciones' => 'NEUMONIA BILATERAL',
            ],
            [
                'comunidad_id' => 11,
                'genero_id' => 2,
                'estado_civil_id' => 1,
                'cedula' => '1003661400',
                'apellidos' => 'RAMOS ESPINOSA',
                'nombres' => 'NOEMI YOLANDA',
                'fecha_nac' => '1994-11-06',
                'fecha_fallecimiento' => '2023-11-06',
                'observaciones' => 'NO REGISTRA CAUSA',
            ],
            [
                'comunidad_id' => 35,
                'genero_id' => 1,
                'estado_civil_id' => 2,
                'cedula' => '1001369873',
                'apellidos' => 'CAMPO YACELGA',
                'nombres' => 'CARLOS',
                'fecha_nac' => '1938-11-08',
                'fecha_fallecimiento' => '2023-11-08',
                'observaciones' => 'FALLO MULTIORGANICRESPIRATORIO',
            ],
            [
                'comunidad_id' => 17,
                'genero_id' => 1,
                'estado_civil_id' => 2,
                'cedula' => '1001040441',
                'apellidos' => 'ESTRADA LANCHIMBA',
                'nombres' => 'CARLOS ELIAS',
                'fecha_nac' => '1954-11-12',
                'fecha_fallecimiento' => '2023-11-12',
                'observaciones' => 'CANCER PROSTATICO',
            ],

            // ═══════════════════════════════════════
            // --- DICIEMBRE 2023 ---
            // ═══════════════════════════════════════
            [
                'comunidad_id' => 4,
                'genero_id' => 1,
                'estado_civil_id' => 2,
                'cedula' => '1001735057',
                'apellidos' => 'MORALES MORALES',
                'nombres' => 'SEGUNDO',
                'fecha_nac' => '1968-12-03',
                'fecha_fallecimiento' => '2023-12-03',
                'observaciones' => 'TUMOR MALIGNO DEL ESTOMAGO',
            ],
            [
                'comunidad_id' => 1,
                'genero_id' => 2,
                'estado_civil_id' => 2,
                'cedula' => '1001636586',
                'apellidos' => 'MALES SANTILLAN',
                'nombres' => 'ROSA ELENA',
                'fecha_nac' => '1966-12-04',
                'fecha_fallecimiento' => '2023-12-04',
                'observaciones' => 'EVENTO CEREBROVASCULAR ISQUIMICO',
            ],
            [
                'comunidad_id' => 6,
                'genero_id' => 1,
                'estado_civil_id' => 4,
                'cedula' => '1000247724',
                'apellidos' => 'MORALES ARIAS',
                'nombres' => 'JOSE SEGUNDO',
                'fecha_nac' => '1941-12-05',
                'fecha_fallecimiento' => '2023-12-05',
                'observaciones' => 'NEUMONIA TUBERCULOSIS PULMUNAR',
            ],
            [
                'comunidad_id' => 15,
                'genero_id' => 1,
                'estado_civil_id' => 4,
                'cedula' => '1001152576',
                'apellidos' => 'CANDO CANDO',
                'nombres' => 'FRANCISCO',
                'fecha_nac' => '1941-12-06',
                'fecha_fallecimiento' => '2023-12-06',
                'observaciones' => 'MUERTE CARDIACA',
            ],
            [
                'comunidad_id' => 22,
                'genero_id' => 1,
                'estado_civil_id' => 2,
                'cedula' => '1002467478',
                'apellidos' => 'ROMERO RAMOS',
                'nombres' => 'LUIS GILBERTO',
                'fecha_nac' => '1971-12-11',
                'fecha_fallecimiento' => '2023-12-11',
                'observaciones' => 'PARO CARDIACO HEMORRAGIA',
            ],

            // ═══════════════════════════════════════
            // --- ENERO 2024 ---
            // ═══════════════════════════════════════
            [
                'comunidad_id' => 34,
                'genero_id' => 2,
                'estado_civil_id' => 2,
                'cedula' => '1001959301',
                'apellidos' => 'LEMA MUENALA',
                'nombres' => 'MARIA CECILIA',
                'fecha_nac' => '1971-01-02',
                'fecha_fallecimiento' => '2024-01-02',
                'observaciones' => 'CANCER DE CUELLO DE UTERO',
            ],
            [
                'comunidad_id' => 36,
                'genero_id' => 1,
                'estado_civil_id' => 4,
                'cedula' => '1000674513',
                'apellidos' => 'COTACACHI VELASQUEZ',
                'nombres' => 'JOSE FRANSISCO',
                'fecha_nac' => '1947-01-02',
                'fecha_fallecimiento' => '2024-01-02',
                'observaciones' => 'INFARTO AGUDO DE MIOCARDO',
            ],
            [
                'comunidad_id' => 24,
                'genero_id' => 2,
                'estado_civil_id' => 4,
                'cedula' => '1001913613',
                'apellidos' => 'LOPEZ LOPEZ',
                'nombres' => 'MARIA DOLORES',
                'fecha_nac' => '1927-01-03',
                'fecha_fallecimiento' => '2024-01-03',
                'observaciones' => 'SENILIDAD',
            ],
            [
                'comunidad_id' => 15,
                'genero_id' => 2,
                'estado_civil_id' => 4,
                'cedula' => '1001176674',
                'apellidos' => 'CASTAÑEDA POTOSI',
                'nombres' => 'DOLORES',
                'fecha_nac' => '1931-01-06',
                'fecha_fallecimiento' => '2024-01-06',
                'observaciones' => 'MUERTE SUBIDA CARDIACA',
            ],
            [
                'comunidad_id' => 34,
                'genero_id' => 2,
                'estado_civil_id' => 4,
                'cedula' => '1000909760',
                'apellidos' => 'CONEJO CONEJO',
                'nombres' => 'MERCEDES',
                'fecha_nac' => '1942-01-07',
                'fecha_fallecimiento' => '2024-01-07',
                'observaciones' => 'CAQNCER DE TIROIDES CANCER DEL HIGADO CON METASIS',
            ],

            // ═══════════════════════════════════════
            // --- FEBRERO 2024 ---
            // ═══════════════════════════════════════
            [
                'comunidad_id' => 25,
                'genero_id' => 2,
                'estado_civil_id' => 3,
                'cedula' => '1702558089',
                'apellidos' => 'VASQUEZ QUINCHI',
                'nombres' => 'LUZ MARIA',
                'fecha_nac' => '1953-02-04',
                'fecha_fallecimiento' => '2024-02-04',
                'observaciones' => 'CANCER DE OVARIO',
            ],
            [
                'comunidad_id' => 14,
                'genero_id' => 2,
                'estado_civil_id' => 3,
                'cedula' => '1000880060',
                'apellidos' => 'ASCANTA ARIAS',
                'nombres' => 'MARIA ELENA',
                'fecha_nac' => '1943-02-05',
                'fecha_fallecimiento' => '2024-02-05',
                'observaciones' => 'INSUFICIENCIA RESPIRATORIA AGUDA HEMORRAGIA INTRACEREBRAL',
            ],
            [
                'comunidad_id' => 23,
                'genero_id' => 2,
                'estado_civil_id' => 2,
                'cedula' => '1001017894',
                'apellidos' => 'MUENALA ARELLANO',
                'nombres' => 'MARIA ZOILA',
                'fecha_nac' => '1958-02-06',
                'fecha_fallecimiento' => '2024-02-06',
                'observaciones' => 'TUMOR MALIGNO DE MAMA',
            ],
            [
                'comunidad_id' => 10,
                'genero_id' => 1,
                'estado_civil_id' => 2,
                'cedula' => '1001894979',
                'apellidos' => 'ULCANGO MALES',
                'nombres' => 'JOSE MARIA',
                'fecha_nac' => '1970-02-12',
                'fecha_fallecimiento' => '2024-02-12',
                'observaciones' => 'PARO RESPIRATORIO,INFARTO MIOCARDIO PARO CARDIO RESPIRATORIO',
            ],
            [
                'comunidad_id' => 24,
                'genero_id' => 2,
                'estado_civil_id' => 3,
                'cedula' => '1001508090',
                'apellidos' => 'PERUGACHI MALDONADO',
                'nombres' => 'FRANCISCA',
                'fecha_nac' => '1941-02-14',
                'fecha_fallecimiento' => '2024-02-14',
                'observaciones' => 'NEOPLASIA MALIGNA DE ESTOMAGO',
            ],
        ];

        $count = 0;
        foreach ($fallecidos as $data) {
            $fallecido = Fallecido::create($data);
            $count++;
            $this->command->info("  ✅ {$fallecido->codigo}: {$fallecido->apellidos} {$fallecido->nombres}");
        }

        $this->command->info("");
        $this->command->info("══════════════════════════════════════════");
        $this->command->info("✅ Seeder de Fallecidos completado: {$count} fallecidos creados");
        $this->command->info("══════════════════════════════════════════");
    }
}
