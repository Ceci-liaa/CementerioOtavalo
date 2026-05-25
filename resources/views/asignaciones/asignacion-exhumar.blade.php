<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold"><i class="fas fa-person-digging me-2"></i> Registrar Exhumación</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('asignaciones.exhumar') }}" id="formExhumar">
    @csrf
    <input type="hidden" name="nicho_id" value="{{ $nicho->id }}">

    <div class="modal-body">
        <div class="alert alert-warning py-2 mb-4 text-xs"><i class="fas fa-exclamation-triangle me-1"></i> <strong>Atención:</strong> Esta acción finalizará la ocupación y liberará el espacio.</div>

        <div class="row g-3">
            <div class="col-12 mb-2">
                <div class="p-2 bg-light border rounded"><strong>Nicho:</strong> {{ $nicho->codigo }} <span class="mx-2">|</span> <strong>Bloque:</strong> {{ $nicho->bloque->descripcion ?? '-' }}</div>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Seleccione el Fallecido a Exhumar <span class="text-danger">*</span></label>
                <select name="fallecido_id" class="form-select form-select-lg" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($fallecidosActivos as $f)
                        <option value="{{ $f->id }}">{{ $f->apellidos }} {{ $f->nombres }} (Ingreso: {{ \Carbon\Carbon::parse($f->pivot->fecha_inhumacion)->format('d/m/Y') }})</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12"><hr class="text-muted"></div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Fecha de Exhumación <span class="text-danger">*</span></label>
                <input type="date" name="fecha_exhumacion" value="{{ date('Y-m-d') }}" class="form-control" required>
            </div>

            <div class="col-md-12">
                <label class="form-label fw-bold">Observación / Destino</label>
                <textarea name="observacion" class="form-control" rows="3" placeholder="Ej: Traslado a osario, retirado por familia..." required></textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-dark" id="btnConfirmarExhumar">Confirmar Exhumación</button>
    </div>
</form>

<script>
    document.getElementById('btnConfirmarExhumar').addEventListener('click', function() {
        const form = document.getElementById('formExhumar');

        // Validar campos obligatorios primero
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // SweetAlert igual que el de eliminar
        Swal.fire({
            title: '¿Está seguro de exhumar?',
            html: 'Esta acción finalizará la ocupación y liberará el espacio en el nicho.<br><small class="text-danger">Esta acción no se puede deshacer.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, exhumar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
</script>