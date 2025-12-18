{{-- CABECERA DEL MODAL (Azul Informativo) --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0">
    <h5 class="modal-title fw-bold">Detalle de Factura</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body pt-3">
    
    {{-- Tarjeta destacada --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-3 p-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.7rem;">Código</small>
            <span class="fw-bold text-dark fs-5">{{ $factura->codigo }}</span>
        </div>
        <div class="text-end">
             <span class="badge bg-{{ $factura->estado == 'PAGADA' ? 'success' : ($factura->estado == 'ANULADA' ? 'danger' : 'warning') }}">
                {{ $factura->estado }}
             </span>
        </div>
    </div>

    <div class="row g-3">
        {{-- Cliente --}}
        <div class="col-12">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Cliente</h6>
        </div>
        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Razón Social</label>
            <div class="fw-bold text-dark">{{ $factura->cliente_nombre }} {{ $factura->cliente_apellido }}</div>
        </div>
        <div class="col-md-6">
            <label class="d-block text-muted small mb-0">Cédula / RUC</label>
            <div>{{ $factura->cliente_cedula ?? 'S/N' }}</div>
        </div>
        @if($factura->socio)
            <div class="col-12">
                <small class="text-success"><i class="fas fa-link me-1"></i>Vinculado al Socio: {{ $factura->socio->nombres }}</small>
            </div>
        @endif

        {{-- Detalle Tabla --}}
        <div class="col-12 mt-3">
            <h6 class="text-primary fw-bold text-xs text-uppercase border-bottom pb-1 mb-2">Items</h6>
            <div class="table-responsive">
                <table class="table table-sm table-striped mb-0 text-sm">
                    <thead>
                        <tr>
                            <th>Desc.</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">P. Unit</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($factura->detalles as $det)
                        <tr>
                            <td>
                                {{ $det->nombre_item }}
                                <br><small class="text-muted">{{ $det->tipo_item }}</small>
                            </td>
                            <td class="text-center">{{ $det->cantidad }}</td>
                            <td class="text-end">$ {{ number_format($det->precio, 2) }}</td>
                            <td class="text-end">$ {{ number_format($det->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-top">
                        <tr>
                            <td colspan="3" class="text-end fw-bold">TOTAL:</td>
                            <td class="text-end fw-bold fs-6">$ {{ number_format($factura->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Auditoría --}}
        <div class="col-12 mt-3 pt-2 border-top d-flex justify-content-between text-xs text-muted">
            <span>Fecha: {{ $factura->fecha->format('d/m/Y') }}</span>
            <span>Creado: {{ $factura->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>