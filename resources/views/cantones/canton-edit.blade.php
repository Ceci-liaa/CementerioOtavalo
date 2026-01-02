{{-- CABECERA DEL MODAL (Estilo Advertencia para Editar) --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Cantón</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('cantones.update', $canton->id) }}">
    @csrf @method('PUT')
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        {{-- Errores --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Código (Solo lectura) --}}
            <div class="col-12">
                <label class="form-label fw-bold text-muted">Código</label>
                <input value="{{ $canton->codigo }}" class="form-control bg-light" readonly>
            </div>

            {{-- Nombre del Cantón --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre del Cantón <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre', $canton->nombre) }}" class="form-control" required maxlength="255">
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>