<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Fallecidos</title>
    <style>
        /* 1. CONFIGURACIÓN DE PÁGINA */
        @page { margin: 0cm 0cm; }

        /* 2. CUERPO */
        body {
            font-family: 'Arial', sans-serif;
            /* AUMENTAMOS el margen superior para que el texto no choque con el encabezado más grande */
            margin-top: 5cm;    
            margin-bottom: 4.5cm; 
            margin-left: 0.8cm;
            margin-right: 0.8cm;
            background-color: #ffffff;
        }

        /* 3. ENCABEZADO */
        header {
            position: fixed;
            top: 0.5cm;
            left: 0cm;
            right: 0cm;
            /* AQUÍ SE CONTROLA LA ALTURA DEL ENCABEZADO */
            height: 3cm;  /* Aumentado de 2cm a 3cm para que no se vea estrecho */
            padding-left: 0.8cm;
            padding-right: 0.8cm;
            z-index: 1000;
        }

        /* 4. PIE DE PÁGINA */
        footer {
            position: fixed;
            bottom: 0cm;
            left: 0cm;
            right: 0cm;
            height: 4.2cm; 
            padding-left: 0.8cm;
            padding-right: 0.8cm;
            z-index: 1000;
        }

        /* La imagen se ajustará automáticamente a la nueva altura de 3cm */
        header img { 
            width: 100%; 
            height: 100%;
            object-fit: contain; /* Asegura que la imagen no se deforme */
        }
        
        footer img { width: 100%; height: auto; }

        /* --- ELEMENTOS DEL PIE DE PÁGINA --- */
        .footer-meta {
            width: 100%; font-size: 10px; color: #333;
            margin-bottom: 5px; border-top: 1px solid #ccc; padding-top: 5px;
        }
        .footer-contacto {
            text-align: center; font-size: 10px; color: #333;
            font-weight: bold; line-height: 1.3; margin-bottom: 5px;
        }
        .email-link { color: #007bff; text-decoration: none; }

        /* --- CONTENIDO DEL REPORTE --- */
        .reporte-titulo {
            text-align: center; font-size: 20px; font-weight: bold;
            color: #333; margin-top: 0px;
            margin-bottom: 20px; text-transform: uppercase;
        }
        .fecha-top {
            text-align: right; font-size: 12px; margin-bottom: 10px; color: #333;
        }

        /* TABLA */
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th {
            background-color: #1c2a48; color: white; padding: 8px 4px;
            text-align: center; text-transform: uppercase; font-size: 10px;
        }
        td {
            padding: 6px 4px; border: 1px solid #ddd; text-align: center;
            color: #333; vertical-align: middle;
        }
        tr:nth-child(even) { background-color: #f2f2f2; }
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
            REPORTE DE FALLECIDOS
            <br>
            <span style="font-size: 14px; font-weight: normal; margin-top: 5px; display: block;">
                {{ $subtitulo ?? 'Reporte General' }}
            </span>
        </div>

        @if ($data->isEmpty())
            <div style="text-align: center; padding: 30px; font-size: 14px; color: #888; border: 2px dashed #ccc;">
                No se encontraron registros para este reporte.
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 30px;">#</th>
                        <th style="width: 60px;">Código</th>
                        <th style="width: 75px;">Cédula</th>
                        <th>Apellidos y Nombres</th>
                        <th style="width: 85px;">Comunidad</th>
                        <th style="width: 65px;">F. Nac.</th>
                        <th style="width: 65px;">F. Fall.</th>
                        <th style="width: 40px;">Edad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td style="font-weight: bold; background-color: #f9f9f9;">
                                {{ $index + 1 }}
                            </td>
                            <td>{{ $row['codigo'] }}</td>
                            <td>{{ $row['cedula'] }}</td>
                            <td style="text-align: left; padding-left: 10px;">{{ $row['nombres_completos'] }}</td>
                            <td>{{ $row['comunidad'] }}</td>
                            <td>{{ $row['fecha_nac'] }}</td>
                            <td>{{ $row['fecha_fall'] }}</td>
                            <td>
                                {{ intval($row['edad']) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div style="margin-top: 20px; font-size: 11px; color: #666; text-align: right;">
                Total de registros: {{ count($data) }}
            </div>
        @endif
    </main>

</body>
</html>