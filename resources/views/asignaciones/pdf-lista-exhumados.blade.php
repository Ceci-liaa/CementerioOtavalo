<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Exhumaciones</title>
    <style>
        /* CONFIGURACIÓN DE PÁGINA */
        @page { margin: 0cm 0cm; }
        body {
            font-family: 'Arial', sans-serif;
            margin: 4.5cm 1.5cm 4cm 1.5cm;
            background-color: #fff;
            font-size: 10px;
        }
        
        /* HEADER Y FOOTER */
        header { position: fixed; top: 0.5cm; left: 0; right: 0; height: 3cm; padding: 0 1.5cm; }
        footer { position: fixed; bottom: 0; left: 0; right: 0; height: 4cm; padding: 0 1.5cm; }
        header img, footer img { width: 100%; height: auto; }

        /* TÍTULOS */
        .titulo { 
            text-align: center; 
            font-size: 16px; 
            font-weight: bold; 
            text-transform: uppercase; 
            border-bottom: 2px solid #6c757d; 
            padding-bottom: 5px; 
            margin-bottom: 15px; 
            color: #495057;
        }
        .fecha-top { text-align: right; font-size: 10px; color: #555; margin-bottom: 5px; }

        /* TABLA */
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { 
            background-color: #343a40; /* Gris oscuro para exhumados */
            color: white; 
            padding: 6px; 
            text-transform: uppercase; 
            font-size: 9px; 
        }
        td { border: 1px solid #ccc; padding: 5px; text-align: center; vertical-align: middle; }
        tr:nth-child(even) { background-color: #f2f2f2; }

        .text-left { text-align: left; padding-left: 8px; }
        .badge { 
            padding: 2px 5px; 
            background-color: #6c757d; 
            color: white; 
            border-radius: 3px; 
            font-size: 8px; 
            font-weight: bold; 
        }
    </style>
</head>
<body>

    <header><img src="{{ public_path('assets/img/encabezado.png') }}" alt="Encabezado"></header>
    <footer><img src="{{ public_path('assets/img/piedepagina.png') }}" alt="Pie de pagina"></footer>

    <main>
        <div class="fecha-top">
            Otavalo, {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY') }}
        </div>

        <div class="titulo">
            HISTORIAL DE EXHUMACIONES
        </div>

        @if($nichos->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 20px;">#</th>
                        <th style="width: 60px;">Nicho</th>
                        <th style="width: 70px;">Cód. Acta</th>
                        <th>Fallecido Exhumado</th>
                        <th style="width: 80px;">Fecha Exhumación</th>
                        <th style="width: 60px;">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @php $contador = 1; @endphp
                    @foreach ($nichos as $nicho)
                        {{-- Recorremos SOLO los exhumados (filtrados en el controlador) --}}
                        @foreach($nicho->fallecidos as $fallecido)
                            <tr>
                                <td>{{ $contador++ }}</td>
                                
                                <td style="font-weight: bold;">
                                    {{ $nicho->codigo }}
                                    <div style="font-size: 8px; font-weight: normal;">
                                        {{ $nicho->bloque->descripcion ?? '' }}
                                    </div>
                                </td>
                                
                                <td style="font-weight: bold;">
                                    {{ $fallecido->pivot->codigo ?? 'S/N' }}
                                </td>

                                <td class="text-left">
                                    {{ $fallecido->apellidos }} {{ $fallecido->nombres }}
                                </td>

                                <td>
                                    @if($fallecido->pivot->fecha_exhumacion)
                                        {{ \Carbon\Carbon::parse($fallecido->pivot->fecha_exhumacion)->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <span class="badge">EXHUMADO</span>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <div style="text-align: right; margin-top: 10px; font-size: 10px; color: #555;">
                Total Registros: {{ $contador - 1 }}
            </div>
        @else
            <div style="text-align: center; padding: 30px; border: 1px dashed #999; color: #666;">
                No se encontraron registros de exhumaciones.
            </div>
        @endif
    </main>
</body>
</html>