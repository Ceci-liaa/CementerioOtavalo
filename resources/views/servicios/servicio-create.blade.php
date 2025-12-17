{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Servicio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('servicios.store') }}">
    @csrf
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        {{-- Mensaje informativo --}}
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código del servicio se genera automáticamente (ej. S001).
        </div>

        {{-- Mostrar errores --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Nombre --}}
            <div class="col-md-8">
                <label class="form-label fw-bold">Nombre del Servicio <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre') }}" class="form-control" placeholder="Ej. Mantenimiento General" required>
            </div>

            {{-- Valor --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Precio Sugerido ($)</label>
                <input type="number" step="0.01" name="valor" value="{{ old('valor') }}" class="form-control" placeholder="0.00">
            </div>

            {{-- Descripción --}}
            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalle breve del servicio...">{{ old('descripcion') }}</textarea>
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>