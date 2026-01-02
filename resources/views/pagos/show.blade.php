{{-- ESTILO DEL ENCABEZADO (MORADO COMO EN LA FOTO) --}}
<div class="modal-header text-white" style="background-color: #6f42c1;">
    <h5 class="modal-title fw-bold">
        <i class="fas fa-file-invoice me-2"></i>Detalle del Recibo #{{ $recibo->id }}
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body bg-light p-0">
    
    {{-- 1. RESUMEN DEL SOCIO (ESTILO BARRA GRIS) --}}
    @php
        $socio = $recibo->socio;
        // Calculamos la deuda actual para mostrarla en el badge
        $aniosPendientes = $socio->anios_deuda;
    @endphp
    <div class="p-3 mb-3" style="background-color: #e9ecef; border-bottom: 1px solid #dee2e6;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-secondary fw-bold d-block mb-1">SOCIO</small>
                <h5 class="fw-bolder mb-0" style="color: #6f42c1;">{{ $socio->apellidos }} {{ $socio->nombres }}</h5>
                <small class="text-muted fw-bold">Cédula: <span class="text-dark">{{ $socio->cedula }}</span></small>
            </div>
            <div class="text-end">
                <small class="text-secondary fw-bold d-block mb-1">ESTADO ACTUAL</small>
                @if(count($aniosPendientes) > 0)
                    <span class="badge bg-danger fs-6 px-3 py-2 shadow-sm">
                        Debe {{ count($aniosPendientes) }} años
                    </span>
                @else
                    <span class="badge bg-success fs-6 px-3 py-2 shadow-sm">
                        ¡Al día!
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-0 p-3">
        {{-- 2. COLUMNA IZQUIERDA: TOTALES Y OBSERVACIÓN --}}
        <div class="col-md-4 pe-md-3 mb-3 mb-md-0">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <small class="text-muted fw-bold text-uppercase mb-2">Total Pagado</small>
                    <h2 class="fw-bold text-success mb-3">${{ number_format($recibo->total, 2) }}</h2>
                    
                    <div class="mb-4">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 fw-bold">
                            <i class="fas fa-calendar-check me-1"></i> {{ $recibo->fecha_pago->format('d/m/Y') }}
                        </span>
                    </div>

                    @if($recibo->observacion)
                        <div class="text-start mt-auto p-2 bg-light rounded border small text-muted">
                            <strong><i class="fas fa-comment-alt me-1"></i>Observación:</strong><br>
                            {{ $recibo->observacion }}
                        </div>
                    @else
                        <div class="text-muted small mt-auto fst-italic">Sin observaciones.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 3. COLUMNA DERECHA: TABLA DE DESGLOSE (PAGADOS Y ADEUDADOS) --}}
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold text-secondary border-bottom py-2">
                    <i class="fas fa-list-ul me-1"></i> Desglose de Años y Deuda
                </div>
                <div class="card-body p-0 table-responsive" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover mb-0 text-center align-middle">
                        <thead class="table-light small text-secondary text-uppercase sticky-top">
                            <tr>
                                <th>Año</th>
                                <th>Estado</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- A) AÑOS PAGADOS EN ESTE RECIBO --}}
                            @foreach($recibo->pagos as $pago)
                            <tr>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success fw-bold border border-success px-2">
                                        {{ $pago->anio_pagado }}
                                    </span>
                                </td>
                                <td><span class="badge bg-success px-2">Pagado</span></td>
                                <td class="fw-bold text-dark">${{ number_format($pago->monto, 2) }}</td>
                            </tr>
                            @endforeach

                            {{-- SEPARADOR SI HAY DEUDA --}}
                            @if(count($aniosPendientes) > 0)
                                <tr>
                                    <td colspan="3" class="bg-light text-muted small fw-bold text-uppercase py-1">
                                        Años aún por pagar
                                    </td>
                                </tr>
                            @endif

                            {{-- B) AÑOS QUE AÚN ADEUDA --}}
                            @foreach($aniosPendientes as $anio)
                            <tr>
                                <td>
                                    <span class="badge bg-danger bg-opacity-10 text-danger fw-bold border border-danger px-2">
                                        {{ $anio }}
                                    </span>
                                </td>
                                <td><span class="badge bg-danger px-2">Pendiente</span></td>
                                <td class="text-muted small fst-italic">---</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer bg-white">
    <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
</div>