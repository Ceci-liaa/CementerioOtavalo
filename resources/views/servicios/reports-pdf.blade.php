<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Servicios</title>
    <style>
        @page { margin: 0cm 0cm; }
        body {
            font-family: 'Arial', sans-serif;
            margin-top: 5cm; margin-bottom: 4.5cm; margin-left: 1.5cm; margin-right: 1.5cm;
            background-color: #ffffff;
        }
        header { position: fixed; top: 0.5cm; left: 0cm; right: 0cm; height: 3cm; padding: 0 1.5cm; z-index: 1000; }
        footer { position: fixed; bottom: 0cm; left: 0cm; right: 0cm; height: 4.2cm; padding: 0 1.5cm; z-index: 1000; }
        header img, footer img { width: 100%; height: auto; }
        
        .footer-meta { width: 100%; font-size: 10px; color: #333; margin-bottom: 5px; border-top: 1px solid #ccc; padding-top: 5px; }
        .footer-contacto { text-align: center; font-size: 10px; color: #333; font-weight: bold; line-height: 1.3; margin-bottom: 5px; }
        .email-link { color: #007bff; text-decoration: none; }
        .reporte-titulo { text-align: center; font-size: 20px; font-weight: bold; color: #1c2a48; margin: 10px 0 20px 0; text-transform: uppercase; }
        .fecha-top { text-align: right; font-size: 12px; margin-bottom: 10px; color: #333; }

        /* TABLA */
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th { background-color: #1c2a48; color: white; padding: 8px 4px; text-align: center; text-transform: uppercase; font-size: 9px; }
        td { padding: 6px 4px; border: 1px solid #ddd; text-align: center; color: #333; vertical-align: middle; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        
        /* Alineación izquierda para textos largos */
        .text-left { text-align: left !important; padding-left: 8px; }
        .text-right { text-align: right !important; padding-right: 8px; }
    </style>
</head>
<body>

    <header>
        <img src="{{ public_path('assets/img/encabezado.png') }}" alt="Encabezado">
    </header>

    <footer>
        <div class="footer-meta">
            <div style="float: left; width: 50%;">Generado por: <b>{{ auth()->user()->name ?? 'Sistema' }}</b></div>
            <div style="float: right; width: 50%; text-align: right;">Otavalo, {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY') }}</div>
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
        <div class="fecha-top">Fecha de emisión: {{ date('d/m/Y H:i') }}</div>
        <div class="reporte-titulo">REPORTE GENERAL DE SERVICIOS</div>

        @if ($data->isEmpty())
            <div style="text-align: center; padding: 30px; font-size: 14px; color: #888;">No se encontraron servicios seleccionados.</div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 25px;">#</th>
                        @foreach ($headings as $index => $heading)
                            {{-- Saltamos el ID (índice 0) --}}
                            @if ($index > 0) 
                                <th 
                                    @if ($heading == 'Código') style="width: 50px;" 
                                    @elseif ($heading == 'Nombre del Servicio') style="text-align: left; padding-left:8px;"
                                    @elseif ($heading == 'Descripción') style="text-align: left; padding-left:8px;"
                                    @elseif ($heading == 'Precio Sugerido') style="width: 70px; text-align: right; padding-right:8px;"
                                    @elseif ($heading == 'Fecha Creación') style="width: 60px;"
                                    @endif
                                >
                                    {{ $heading }}
                                </th>
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            {{-- Columna contadora --}}
                            <td style="font-weight: bold; background-color: #e9ecef;">{{ $index + 1 }}</td>
                            
                            {{-- 
                                Convertimos objeto a array y saltamos el ID (index 0).
                                Los índices resultantes del array_slice serán:
                                0: Código
                                1: Nombre
                                2: Descripción
                                3: Valor
                                4: Fecha
                            --}}
                            @foreach (array_slice((array)$row, 1) as $cellIndex => $cell)
                                <td 
                                    @if ($cellIndex == 1 || $cellIndex == 2) class="text-left"     {{-- Nombre y Descripción a la izquierda --}}
                                    @elseif ($cellIndex == 3) class="text-right"   {{-- Valor a la derecha --}}
                                    @endif
                                >
                                    {{ $cell }}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top: 20px; font-size: 10px; color: #666; text-align: right;">
                Total de registros: {{ count($data) }}
            </div>
        @endif
    </main>
</body>
</html>
