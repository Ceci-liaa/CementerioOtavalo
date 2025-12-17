{{-- CABECERA (Amarilla para distinguir Editar) --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Servicio</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('servicios.update', $servicio) }}">
    @csrf @method('PUT')

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
                <input value="{{ $servicio->codigo }}" class="form-control bg-light" readonly>
            </div>

            {{-- Nombre --}}
            <div class="col-md-8">
                <label class="form-label fw-bold">Nombre del Servicio <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre', $servicio->nombre) }}" class="form-control" required>
            </div>

            {{-- Valor --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Precio Sugerido ($)</label>
                <input type="number" step="0.01" name="valor" value="{{ old('valor', $servicio->valor) }}" class="form-control">
            </div>

            {{-- Descripción --}}
            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $servicio->descripcion) }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>