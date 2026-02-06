<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Asignaciones</title>
    <style>
        /* 1. CONFIGURACIÓN DE PÁGINA */
        @page { margin: 0cm 0cm; }

        /* 2. CUERPO */
        body {
            font-family: 'Arial', sans-serif;
            margin-top: 5cm;    
            margin-bottom: 4.5cm; 
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            background-color: #ffffff;
            font-size: 10px; /* Tamaño base */
        }

        /* 3. ENCABEZADO */
        header {
            position: fixed;
            top: 0.5cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
            padding-left: 1.5cm;
            padding-right: 1.5cm;
            z-index: 1000;
        }

        /* 4. PIE DE PÁGINA */
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 4.2cm; 
            padding-left: 1.5cm;
            padding-right: 1.5cm;
            z-index: 1000;
        }

        header img, footer img {
            width: 100%;
            height: auto;
        }

        /* --- ELEMENTOS DEL PIE DE PÁGINA --- */
        .footer-meta {
            width: 100%;
            font-size: 9px;
            color: #333;
            margin-bottom: 5px;
            border-top: 1px solid #ccc; 
            padding-top: 5px;
        }

        .footer-contacto {
            text-align: center;
            font-size: 9px;
            color: #333;
            font-weight: bold;
            line-height: 1.3;
            margin-bottom: 5px;
        }

        .email-link {
            color: #007bff;
            text-decoration: none;
        }

        /* --- TÍTULO --- */
        .reporte-titulo {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1c2a48;
            margin-top: 10px;
            margin-bottom: 20px;
            text-transform: uppercase;
            border-bottom: 2px solid #1c2a48;
            padding-bottom: 5px;
        }

        .fecha-top {
            text-align: right;
            font-size: 11px;
            margin-bottom: 5px;
            color: #555;
        }

        /* --- TABLA --- */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #1c2a48;
            color: white;
            padding: 8px 4px;
            text-align: center;
            text-transform: uppercase;
            font-size: 9px;
            font-weight: bold;
        }
        td {
            padding: 6px 4px;
            border: 1px solid #ddd;
            text-align: center;
            color: #333;
            vertical-align: middle;
        }
        tr:nth-child(even) { background-color: #f8f9fa; }

        /* UTILIDADES PARA CELDAS */
        .text-left { text-align: left; padding-left: 8px; }
        
        /* ESTILO PARA FILAS INTERNAS (IMPORTANTE PARA ALINEAR CÓDIGO CON NOMBRE) */
        .sub-item { 
            display: block; 
            border-bottom: 1px solid #eee; 
            padding: 4px 0; 
            font-size: 9px; 
            min-height: 12px;
        }
        .sub-item:last-child { border-bottom: none; }
        
        /* BADGES (Etiquetas de estado) */
        .badge {
            display: inline-block;
            padding: 3px 6px;
            font-size: 8px;
            font-weight: bold;
            border-radius: 4px;
            color: white;
            background-color: #6c757d; 
        }
        .bg-disponible { background-color: #198754; } 
        .bg-ocupado { background-color: #e6a23c; color: white; } /* Amarillo más oscuro para impresión */
        .bg-lleno { background-color: #dc3545; } 

        .nicho-meta { font-size: 8px; color: #666; display: block; margin-top: 2px; font-style: italic;}
    </style>
</head>
<body>

    <header>
        <img src="{{ public_path('assets/img/encabezado.png') }}" alt="Encabezado">
    </header>

    <footer>
        <div class="footer-meta">
            <div style="float: left; width: 50%;">
                Generado por: {{ auth()->user()->name ?? 'Sistema' }}
            </div>
            <div style="float: right; width: 50%; text-align: right;">
                Otavalo, {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY') }}
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer-contacto">
            <div>06) 2-927-663</div>
            <div><a href="#" class="email-link">unoricosamashunchik@gmail.com</a></div>
            <div>Calle Las Almas y Bolívar</div>
        </div>

        <img src="{{ public_path('assets/img/piedepagina.png') }}" alt="Pie de página">
    </footer>

    <main>
        <div class="fecha-top">
            Otavalo, {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY') }}
        </div>

        <div class="reporte-titulo">
            REPORTE GENERAL DE ASIGNACIONES
        </div>

        @if ($nichos->isEmpty())
            <div style="text-align: center; padding: 40px; border: 2px dashed #ccc; color: #777;">
                <p>No se encontraron registros de asignaciones activas.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 25px;">#</th>
                        <th style="width: 60px;">Nicho</th>
                        <th style="width: 80px;">Cód. Acta</th>
                        <th>Fallecido(s) Asignados</th>
                        <th>Socio Responsable</th>
                        <th style="width: 70px;">Estado</th>
                        <th style="width: 30px;">Ocup.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nichos as $index => $nicho)
                        @php 
                            // Filtramos solo fallecidos activos (no exhumados)
                            $ocupantes = $nicho->fallecidos->where('pivot.fecha_exhumacion', null);
                        @endphp
                        <tr>
                            {{-- 1. Contador --}}
                            <td style="font-weight: bold;">{{ $index + 1 }}</td>

                            {{-- 2. Nicho --}}
                            <td style="font-weight: bold;">
                                {{ $nicho->codigo }}
                                <span class="nicho-meta">{{ $nicho->bloque->descripcion ?? '' }}</span>
                            </td>

                            {{-- 3. Código de Acta (Usamos sub-item para alinear con los nombres) --}}
                            <td>
                                @forelse($ocupantes as $f)
                                    <span class="sub-item" style="font-weight: bold; color: #333;">
                                        {{ $f->pivot->codigo ?? 'S/N' }}
                                    </span>
                                @empty
                                    <span class="sub-item">-</span>
                                @endforelse
                            </td>

                            {{-- 4. Nombres de Fallecidos --}}
                            <td class="text-left">
                                @forelse($ocupantes as $f)
                                    <span class="sub-item">
                                        {{ $f->apellidos }} {{ $f->nombres }}
                                    </span>
                                @empty
                                    <span class="sub-item" style="color:#999;">-- Vacío --</span>
                                @endforelse
                            </td>

                            {{-- 5. Socio Responsable --}}
                            <td class="text-left">
                                @if($nicho->socios->isNotEmpty())
                                    {{ $nicho->socios->first()->apellidos }} {{ $nicho->socios->first()->nombres }}
                                @else
                                    <span style="color: #999; font-style: italic;">Sin Asignar</span>
                                @endif
                            </td>

                            {{-- 6. Estado --}}
                            <td>
                                @php
                                    $clase = match($nicho->estado) {
                                        'DISPONIBLE' => 'bg-disponible',
                                        'OCUPADO' => 'bg-ocupado',
                                        'LLENO' => 'bg-lleno',
                                        default => ''
                                    };
                                @endphp
                                <span class="badge {{ $clase }}">{{ $nicho->estado }}</span>
                            </td>

                            {{-- 7. Cantidad --}}
                            <td>
                                {{ $ocupantes->count() }}/3
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="text-align: right; margin-top: 10px; font-size: 10px; color: #555;">
                <strong>Total de Nichos Asignados:</strong> {{ count($nichos) }}
            </div>
        @endif
    </main>

</body>
</html>