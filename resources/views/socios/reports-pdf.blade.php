<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Socios</title>
    <style>
        /* 1. CONFIGURACIÓN DE PÁGINA */
        @page {
            margin: 0cm 0cm;
        }

        /* 2. CUERPO */
        body {
            font-family: 'Arial', sans-serif;
            margin-top: 3.5cm;
            margin-bottom: 3cm;
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            background-color: #ffffff;
        }

        /* 3. ENCABEZADO FIJO */
        header {
            position: fixed;
            top: 0.5cm;
            left: 0cm;
            right: 0cm;
            height: 3cm;
            padding-left: 1.5cm;
            padding-right: 1.5cm;
            text-align: center;
            z-index: 1000;
        }

        /* 4. PIE DE PÁGINA FIJO */
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 2.5cm; 
            padding-left: 1.5cm;
            padding-right: 1.5cm;
            z-index: 1000;
        }

        header img, footer img {
            width: 100%;
            height: auto;
            max-height: 2.5cm;
        }

        /* --- ESTILOS DEL CONTENIDO --- */
        .fecha-top {
            text-align: right;
            font-size: 11px;
            color: #333;
            margin-bottom: 10px;
        }

        .reporte-titulo {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1c2a48;
            margin-bottom: 15px;
            text-transform: uppercase;
            border-bottom: 2px solid #1c2a48;
            padding-bottom: 8px;
            display: block;
        }

        /* --- TABLA (Ajuste de anchos) --- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px; 
            margin-top: 5px;
            table-layout: fixed; /* Importante para respetar los anchos definidos */
        }
        
        th {
            background-color: #1c2a48;
            color: white;
            padding: 6px 2px;
            text-align: center;
            text-transform: uppercase;
            font-size: 8px;
            border: 1px solid #1c2a48;
            vertical-align: middle;
            overflow: hidden; /* Evita desbordamiento */
        }
        
        td {
            padding: 4px 3px;
            border: 1px solid #ccc;
            text-align: center;
            color: #333;
            vertical-align: middle;
            line-height: 1.1;
            word-wrap: break-word; /* Permite que el texto baje de línea si es largo */
        }
        
        .col-index { background-color: #e9ecef; font-weight: bold; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        
        .text-left { text-align: left !important; padding-left: 5px; }
        .text-bold { font-weight: bold; }
        
        /* Etiquetas */
        .tag-exonerado { color: #198754; font-weight: bold; }
        .tag-fallecido { background-color: #000; color: #fff; padding: 2px 4px; border-radius: 2px; font-size: 8px; }

        /* FOOTER INFO */
        .footer-meta {
            width: 100%;
            font-size: 9px;
            color: #444;
            margin-bottom: 2px;
            border-top: 1px solid #ccc; 
            padding-top: 4px;
        }
        .footer-contacto {
            text-align: center;
            font-size: 9px;
            color: #333;
            font-weight: bold;
            line-height: 1.2;
            margin-bottom: 4px;
        }
        .email-link { color: #007bff; text-decoration: none; }

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
            <span>06) 2-927-663</span> <br> 
            <a href="#" class="email-link">unoricosamashunchik@gmail.com</a> <br>
            <span>Calle Las Almas y Bolívar</span>
        </div>

        <img src="{{ public_path('assets/img/piedepagina.png') }}" alt="Pie de página">
    </footer>

    <main>
        <div class="fecha-top">
            Otavalo, {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [del] YYYY') }}
        </div>

        <div class="reporte-titulo">
            REPORTE GENERAL DE SOCIOS
        </div>

        @if ($data->isEmpty())
            <div style="text-align: center; padding: 40px; font-size: 14px; color: #777; border: 2px dashed #ddd; margin-top: 20px;">
                No se encontraron socios seleccionados para el reporte.
            </div>
        @else
            <table>
                {{-- Definición de anchos de columna (Total debe sumar ~100% o dejar 'auto' para nombres) --}}
                <colgroup>
                    <col style="width: 25px;">  <col style="width: 45px;">  <col style="width: 60px;">  <col style="width: auto;">  <col style="width: 50px;">  <col style="width: 75px;">  <col style="width: 85px;">  <col style="width: 25px;">  <col style="width: 50px;">  <col style="width: 55px;">  <col style="width: 40px;">  <col style="width: 35px;">  <col style="width: 30px;">  </colgroup>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Cédula</th>
                        <th>Apellidos y Nombres</th>
                        <th>Nacim.</th>
                        <th>Ubicación</th>
                        <th>Dirección</th>
                        <th>Edad</th>
                        <th>Beneficio</th>
                        <th>Teléfono</th>
                        <th>Cond.</th>
                        <th>Nichos</th> {{-- NUEVA COLUMNA --}}
                        <th>Est.</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td class="col-index">{{ $index + 1 }}</td>
                            <td class="text-bold">{{ $row['codigo'] }}</td>
                            <td>{{ $row['cedula'] }}</td>
                            
                            {{-- Nombre alineado a izquierda y en negrita --}}
                            <td class="text-left text-bold">{{ $row['nombres'] }}</td>
                            
                            <td>{{ $row['fecha_nac'] }}</td>
                            <td class="text-left" style="font-size: 8px;">{{ $row['ubicacion'] }}</td>
                            <td class="text-left" style="font-size: 8px;">{{ $row['direccion'] }}</td>
                            <td>{{ $row['edad'] }}</td>
                            
                            <td style="font-size: 8px;">
                                @if($row['beneficio'] == 'Exonerado')
                                    <span class="tag-exonerado">EXONERADO</span>
                                @else
                                    {{ $row['beneficio'] }}
                                @endif
                            </td>
                            
                            <td>{{ $row['telefono'] }}</td>
                            <td style="font-size: 8px;">{{ $row['condicion'] ?? 'Ninguna' }}</td>
                            
                            {{-- DATO DE NICHOS --}}
                            <td style="font-weight: bold; background-color: #f0f8ff;">
                                {{ $row['total_nichos'] ?? 0 }}
                            </td>

                            <td>
                                @if(strtolower($row['estatus']) == 'vivo')
                                    Vivo
                                @else
                                    <span class="tag-fallecido">FAL</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 15px; font-size: 10px; color: #555; text-align: right; border-top: 1px solid #eee; padding-top: 5px;">
                <strong>Total Registros:</strong> {{ count($data) }} socios listados.
            </div>
        @endif
    </main>

    {{-- Script de paginación (Solo si usas DomPDF con CPDF habilitado) --}}
    <script type="text/php">
        if ( isset($pdf) ) {
            $pdf->page_script('
                $font = $fontMetrics->get_font("Arial, Helvetica, sans-serif", "normal");
                $pdf->text(520, 790, "Página " . $PAGE_NUM . " de " . $PAGE_COUNT, $font, 9);
            ');
        }
    </script>
</body>
</html>