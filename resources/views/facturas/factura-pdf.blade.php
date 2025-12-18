<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura {{ $factura->codigo }}</title>
    <style>
        /* 1. CONFIGURACIÓN DE PÁGINA (Igual al reporte) */
        @page {
            margin: 0cm 0cm;
        }

        /* 2. CUERPO */
        body {
            font-family: 'Arial', sans-serif;
            margin-top: 4.5cm;     /* Ajustado levemente para que no choque con header */
            margin-bottom: 4.5cm; 
            margin-left: 1.5cm;
            margin-right: 1.5cm;
            background-color: #ffffff;
            font-size: 12px;
            color: #333;
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
            font-size: 10px;
            color: #333;
            margin-bottom: 5px;
            border-top: 1px solid #ccc; 
            padding-top: 5px;
        }

        .footer-contacto {
            text-align: center;
            font-size: 10px;
            color: #333;
            font-weight: bold;
            line-height: 1.3;
            margin-bottom: 5px;
        }

        /* --- ESTILOS ESPECÍFICOS DE FACTURA --- */
        
        /* Tabla de Información (Cliente / Factura) - Sin bordes */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            border: none;
            padding: 5px;
            vertical-align: top;
        }
        
        .caja-cliente {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }

        .titulo-factura {
            text-align: right;
            color: #1c2a48;
            border-bottom: 2px solid #1c2a48;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .titulo-factura h2 {
            margin: 0;
            font-size: 24px;
        }
        .titulo-factura span {
            font-size: 14px;
            color: #555;
        }

        /* Tabla de Detalles (Items) */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 10px;
        }
        
        .items-table th {
            background-color: #1c2a48;
            color: white;
            padding: 8px;
            text-align: center;
            text-transform: uppercase;
            font-size: 10px;
        }

        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: middle;
        }

        /* Clases de utilidad */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        /* Filas alternas */
        .items-table tr:nth-child(even) { background-color: #f2f2f2; }

        /* Totales */
        .total-section {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0px; /* Pegado a la tabla */
        }
        .total-section td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .total-label {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: right;
            color: #1c2a48;
        }
        .total-value {
            font-weight: bold;
            text-align: right;
            font-size: 14px;
        }

        /* Marca de Agua para Estados */
        .watermark {
            position: fixed;
            top: 40%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(255, 0, 0, 0.15);
            font-weight: bold;
            border: 5px solid rgba(255, 0, 0, 0.15);
            padding: 10px 40px;
            z-index: -1;
            text-transform: uppercase;
        }

    </style>
</head>
<body>

    {{-- MARCA DE AGUA SI NO ESTÁ PAGADA O EMITIDA --}}
    @if($factura->estado == 'ANULADA')
        <div class="watermark">ANULADA</div>
    @elseif($factura->estado == 'PENDIENTE')
        <div class="watermark">BORRADOR</div>
    @endif

    <header>
        <img src="{{ public_path('assets/img/encabezado.png') }}" alt="Encabezado">
    </header>

    <footer>
        <div class="footer-meta">
            <div style="float: left; width: 50%;">
                Emitido por: {{ auth()->user()->name ?? 'Sistema' }}
            </div>
            <div style="float: right; width: 50%; text-align: right;">
                Impreso el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
            </div>
            <div style="clear: both;"></div>
        </div>

        <div class="footer-contacto">
            <div>Cementerio Municipal de Otavalo</div>
            <div>Calle Las Almas y Bolívar | Telf: 06 2-927-663</div>
            <div>unoricosamashunchik@gmail.com</div>
        </div>

        <img src="{{ public_path('assets/img/piedepagina.png') }}" alt="Pie de página">
    </footer>

    <main>
        {{-- TABLA DE INFORMACIÓN SUPERIOR (Layout de 2 columnas) --}}
        <table class="info-table">
            <tr>
                {{-- COLUMNA IZQUIERDA: DATOS CLIENTE --}}
                <td width="55%">
                    <div class="caja-cliente">
                        <div style="color:#1c2a48; font-weight:bold; border-bottom:1px solid #ccc; margin-bottom:5px; font-size:11px;">
                            CLIENTE / RAZÓN SOCIAL
                        </div>
                        <div style="font-size: 12px; margin-bottom: 3px;">
                            <strong>{{ $factura->cliente_nombre }} {{ $factura->cliente_apellido }}</strong>
                        </div>
                        <div style="font-size: 11px; line-height: 1.4;">
                            <strong>C.I./RUC:</strong> {{ $factura->cliente_cedula ?? 'Consumidor Final' }}<br>
                            <strong>Teléfono:</strong> {{ $factura->cliente_telefono ?? 'N/A' }}<br>
                            <strong>Email:</strong> {{ $factura->cliente_email ?? 'N/A' }}
                        </div>
                        @if($factura->socio)
                            <div style="margin-top:5px; font-size:10px; color:#007bff;">
                                * Vinculado al Socio: {{ $factura->socio->nombres }}
                            </div>
                        @endif
                    </div>
                </td>

                {{-- COLUMNA DERECHA: DATOS FACTURA --}}
                <td width="45%" style="padding-left: 20px;">
                    <div class="titulo-factura">
                        <h2>FACTURA</h2>
                        <span>N° {{ $factura->codigo }}</span>
                    </div>
                    <table style="width: 100%; font-size: 11px; text-align: right;">
                        <tr>
                            <td style="border:none; padding:2px;"><strong>Fecha de Emisión:</strong></td>
                            <td style="border:none; padding:2px;">{{ $factura->fecha->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td style="border:none; padding:2px;"><strong>Estado:</strong></td>
                            <td style="border:none; padding:2px;">{{ $factura->estado }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- TABLA DE DETALLES --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50px;">Cant.</th>
                    <th>Descripción</th>
                    <!-- <th style="width: 80px;">Tipo</th> -->
                    <th style="width: 90px;" class="text-right">P. Unitario</th>
                    <th style="width: 90px;" class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($factura->detalles as $detalle)
                    <tr>
                        <td class="text-center">{{ $detalle->cantidad }}</td>
                        <td>
                            {{ $detalle->nombre_item }}
                        </td>
                        <!-- <td class="text-center" style="font-size: 9px; color: #666;">
                            {{ $detalle->tipo_item }}
                        </td> -->
                        <td class="text-right">$ {{ number_format($detalle->precio, 2) }}</td>
                        <td class="text-right">$ {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
                
                {{-- Rellenar filas vacías si son pocas (Opcional para estética) --}}
                @for($i = count($factura->detalles); $i < 6; $i++)
                    <tr>
                        <td style="color: white;">.</td>
                        <td></td>
                        <!-- <td></td> -->
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        {{-- TOTALES (Pegado a la tabla de arriba) --}}
        <table class="total-section">
            <tr>
                <td style="border:none;" width="70%">
                    {{-- Espacio para observaciones o firmas si deseas --}}
                    <div style="font-size: 10px; color: #777; margin-top: 10px;">
                        <strong>Observaciones:</strong><br>
                        Documento generado electrónicamente por la aplicación web de gestión de la Organización UNORICO SAMASHUNCHIK.
                    </div>
                </td>
                <td width="15%" class="total-label">TOTAL</td>
                <td width="15%" class="total-value">$ {{ number_format($factura->total, 2) }}</td>
            </tr>
        </table>

    </main>

</body>
</html>