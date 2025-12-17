<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detalle del Servicio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row g-3">
        {{-- Código y Precio destacados --}}
        <div class="col-12 bg-light p-2 rounded border mb-2 d-flex justify-content-between align-items-center">
             <div>
                 <small class="text-muted d-block">Código</small>
                 <span class="fw-bold text-primary">{{ $servicio->codigo }}</span>
             </div>
             <div class="text-end">
                 <small class="text-muted d-block">Precio Sugerido</small>
                 <span class="fw-bold text-success">
                     {{ $servicio->valor ? '$ ' . number_format($servicio->valor, 2) : 'No definido' }}
                 </span>
             </div>
        </div>

        <div class="col-12">
            <label class="text-muted small d-block">Nombre del Servicio</label>
            <div class="fw-semibold">{{ $servicio->nombre }}</div>
        </div>

        <div class="col-12">
            <label class="text-muted small d-block">Descripción</label>
            <div class="p-2 border rounded bg-light text-sm text-secondary">
                {{ $servicio->descripcion ?? 'Sin descripción registrada.' }}
            </div>
        </div>

        <div class="col-12"><hr class="my-1 text-muted"></div>

        <div class="col-md-6">
            <label class="text-muted small d-block">ID Interno</label>
            <div class="small">{{ $servicio->id }}</div>
        </div>
        <div class="col-md-6">
            <label class="text-muted small d-block">Fecha Creación</label>
            <div class="small">{{ $servicio->created_at?->format('d/m/Y H:i') }}</div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>