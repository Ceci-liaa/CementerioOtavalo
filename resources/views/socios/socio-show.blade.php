<div class="modal-header bg-info text-white">
    <h5 class="modal-title fw-bold">Detalle del Socio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row g-3">
        {{-- Encabezado --}}
        <div class="col-12 bg-light p-3 rounded border mb-2 d-flex justify-content-between align-items-center">
             <div>
                 <small class="text-muted d-block fw-bold">CÓDIGO</small>
                 <span class="fs-5 fw-bolder text-primary">{{ $socio->codigo }}</span>
             </div>
             <div class="text-end">
                 <small class="text-muted d-block fw-bold">CÉDULA</small>
                 <span class="fs-5 fw-bold text-dark">{{ $socio->cedula }}</span>
             </div>
        </div>

        {{-- Datos Personales --}}
        <div class="col-md-6">
            <label class="text-muted small fw-bold">Apellidos y Nombres</label>
            <div class="fs-6 text-dark">{{ $socio->apellidos }} {{ $socio->nombres }}</div>
        </div>
        <div class="col-md-3">
            <label class="text-muted small fw-bold">Edad</label>
            <div class="fs-6 fw-bold text-dark">{{ $socio->edad }} años</div>
        </div>
        <div class="col-md-3">
            <label class="text-muted small fw-bold">Nacimiento</label>
            <div class="fs-6 text-dark">{{ optional($socio->fecha_nac)->format('d/m/Y') ?? '—' }}</div>
        </div>

        {{-- Fila 2 --}}
        <div class="col-md-4">
            <label class="text-muted small fw-bold">Género</label>
            <div class="fs-6 text-dark">{{ $socio->genero?->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-muted small fw-bold">Estado civil</label>
            <div class="fs-6 text-dark">{{ $socio->estadoCivil?->nombre ?? '—' }}</div>
        </div>

        {{-- ========================================================= --}}
        {{-- CORRECCIÓN 1: REPRESENTANTE (Letras oscuras para leerse bien) --}}
        {{-- ========================================================= --}}
        <div class="col-md-4">
            <label class="text-dark small fw-bolder d-block">¿ES REPRESENTANTE?</label>
            @if($socio->es_representante)
                <span class="badge bg-success fs-6 px-3 py-2 fw-bolder">SÍ</span>
            @else
                {{-- Agregado 'text-dark' para que las letras sean NEGRAS --}}
                <span class="badge bg-secondary text-dark fs-6 px-3 py-2 fw-bolder">NO</span>
            @endif
        </div>

        <div class="col-12"><hr class="my-2 text-muted opacity-50"></div>

        {{-- DATOS INSTITUCIONALES --}}
        <div class="col-md-4">
            <label class="text-muted small fw-bold">Fecha Inscripción</label>
            <div class="fs-6 fw-bold text-dark">{{ optional($socio->fecha_inscripcion)->format('d/m/Y') ?? '—' }}</div>
        </div>

        {{-- ========================================================= --}}
        {{-- CORRECCIÓN 2: BENEFICIO (Letras oscuras para 'Sin Subsidio') --}}
        {{-- ========================================================= --}}
        <div class="col-md-4">
            <label class="text-dark small fw-bolder d-block">BENEFICIO ACTUAL</label>
            @if($socio->tipo_beneficio == 'exonerado')
                <span class="badge bg-success fs-6 px-3 py-2 fw-bolder">EXONERADO</span>
            @elseif($socio->tipo_beneficio == 'con_subsidio')
                {{-- Para 'Con Subsidio' también aseguramos que se lea --}}
                <span class="badge bg-primary text-white fs-6 px-3 py-2 fw-bolder">CON SUBSIDIO</span>
            @else
                {{-- Agregado 'text-dark' para que 'SIN SUBSIDIO' sea negro --}}
                <span class="badge bg-secondary text-dark fs-6 px-3 py-2 fw-bolder">SIN SUBSIDIO</span>
            @endif
        </div>

        <div class="col-md-4">
            <label class="text-muted small fw-bold">Fecha Exoneración</label>
            <div class="fs-6 text-dark">{{ optional($socio->fecha_exoneracion)->format('d/m/Y') ?? '—' }}</div>
        </div>

        <div class="col-12"><hr class="my-2 text-muted opacity-50"></div>

        {{-- Ubicación --}}
        <div class="col-md-4">
            <label class="text-muted small fw-bold">Comunidad</label>
            <div class="fs-6 fw-bold text-dark">{{ $socio->comunidad?->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-muted small fw-bold">Parroquia</label>
            <div class="fs-6 text-dark">{{ $socio->comunidad?->parroquia?->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-muted small fw-bold">Cantón</label>
            <div class="fs-6 text-dark">{{ $socio->comunidad?->parroquia?->canton?->nombre ?? '—' }}</div>
        </div>

        <div class="col-md-12 mt-2">
            <label class="text-muted small fw-bold">Dirección</label>
            <div class="fs-6 text-dark">{{ $socio->direccion ?? '—' }}</div>
        </div>

        <div class="col-md-6 mt-2">
            <label class="text-muted small fw-bold">Teléfono</label>
            <div class="fs-6 text-dark">{{ $socio->telefono ?? '—' }}</div>
        </div>
        <div class="col-md-6 mt-2">
            <label class="text-muted small fw-bold">Email</label>
            <div class="fs-6 text-dark">{{ $socio->email ?? '—' }}</div>
        </div>
        
        <div class="col-12 bg-light p-2 rounded mt-3 text-center border">
            <small class="text-muted">
                Registrado el {{ $socio->created_at?->format('d/m/Y H:i') }} 
                por <strong>{{ $socio->creador?->name ?? 'Sistema' }}</strong>
            </small>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
</div>