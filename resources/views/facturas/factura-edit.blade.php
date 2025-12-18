{{-- CABECERA DEL MODAL (Amarillo Advertencia) --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Factura {{ $factura->codigo }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('facturas.update', $factura) }}">
    @csrf @method('PUT')
    
    <div class="modal-body">
        
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
                <label class="form-label fw-bold text-muted small">Código Único</label>
                <input value="{{ $factura->codigo }}" class="form-control bg-light" readonly>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small">Estado</label>
                <select name="estado" class="form-select">
                    @foreach (['PENDIENTE','EMITIDA','PAGADA','ANULADA'] as $st)
                        <option value="{{ $st }}" @selected(old('estado', $factura->estado) == $st)>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small">Total (No editable aquí)</label>
                <input value="$ {{ number_format($factura->total, 2) }}" class="form-control bg-light" readonly>
            </div>

            {{-- Datos Cliente Editables --}}
            <div class="col-12 mt-3">
                 <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1">Datos del Cliente</h6>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small">Nombres *</label>
                <input name="cliente_nombre" value="{{ old('cliente_nombre', $factura->cliente_nombre) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Apellidos</label>
                <input name="cliente_apellido" value="{{ old('cliente_apellido', $factura->cliente_apellido) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Cédula</label>
                <input name="cliente_cedula" value="{{ old('cliente_cedula', $factura->cliente_cedula) }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold small">Teléfono</label>
                <input name="cliente_telefono" value="{{ old('cliente_telefono', $factura->cliente_telefono) }}" class="form-control">
            </div>
            <div class="col-12">
                <label class="form-label fw-bold small">Email</label>
                <input name="cliente_email" value="{{ old('cliente_email', $factura->cliente_email) }}" class="form-control">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar Factura</button>
    </div>
</form>