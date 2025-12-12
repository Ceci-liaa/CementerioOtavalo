<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detalle del Socio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row g-3">
        {{-- Código y Cédula destacados --}}
        <div class="col-12 bg-light p-2 rounded border mb-2 d-flex justify-content-between align-items-center">
             <div>
                 <small class="text-muted d-block">Código</small>
                 <span class="fw-bold text-primary">{{ $socio->codigo }}</span>
             </div>
             <div class="text-end">
                 <small class="text-muted d-block">Cédula</small>
                 <span class="fw-bold">{{ $socio->cedula }}</span>
             </div>
        </div>

        <div class="col-md-6">
            <label class="text-muted small d-block">Apellidos</label>
            <div class="fw-semibold">{{ $socio->apellidos }}</div>
        </div>
        <div class="col-md-6">
            <label class="text-muted small d-block">Nombres</label>
            <div class="fw-semibold">{{ $socio->nombres }}</div>
        </div>

        <div class="col-md-4">
            <label class="text-muted small d-block">Género</label>
            <div>{{ $socio->genero?->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-muted small d-block">Estado civil</label>
            <div>{{ $socio->estadoCivil?->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-muted small d-block">Fecha Nacimiento</label>
            <div>{{ optional($socio->fecha_nac)->format('d/m/Y') ?? '—' }}</div>
        </div>

        <div class="col-12"><hr class="my-1 text-muted"></div>

        <div class="col-md-4">
            <label class="text-muted small d-block">Comunidad</label>
            <div class="fw-semibold">{{ $socio->comunidad?->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-muted small d-block">Parroquia</label>
            <div>{{ $socio->comunidad?->parroquia?->nombre ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-muted small d-block">Cantón</label>
            <div>{{ $socio->comunidad?->parroquia?->canton?->nombre ?? '—' }}</div>
        </div>

        <div class="col-md-4">
            <label class="text-muted small d-block">Teléfono</label>
            <div>{{ $socio->telefono ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-muted small d-block">Email</label>
            <div>{{ $socio->email ?? '—' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-muted small d-block">Representante</label>
            <div>
                {!! $socio->es_representante ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}
            </div>
        </div>
        
        <div class="col-12">
            <label class="text-muted small d-block">Dirección</label>
            <div>{{ $socio->direccion ?? '—' }}</div>
        </div>

        <div class="col-12"><hr class="my-1 text-muted"></div>

        <div class="col-md-6">
            <label class="text-muted small d-block">Registrado por</label>
            <div class="small">{{ $socio->creador?->name ?? 'Sistema' }}</div>
        </div>
        <div class="col-md-6">
            <label class="text-muted small d-block">Fecha Registro</label>
            <div class="small">{{ $socio->created_at?->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>