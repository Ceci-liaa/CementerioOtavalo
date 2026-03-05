<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">
        <i class="fas fa-edit me-2"></i>Corregir Asignación
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('asignaciones.update', $nicho->identificacion) }}">
    @csrf @method('PUT')

    @php
        $socioActual = $nicho->socios->first();
        $fallecidoActual = $nicho->fallecidos->where('pivot.fecha_exhumacion', null)->first();
    @endphp

    <input type="hidden" name="fallecido_anterior_id" value="{{ $fallecidoActual->id ?? '' }}">

    <div class="modal-body pb-3">
        {{-- Información de Referencia (Solo Lectura) --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold text-muted small text-uppercase mb-1">Nicho Seleccionado</label>
                <div class="p-2 border rounded bg-light fw-bold text-dark">
                    <i class="fas fa-cube text-warning me-2"></i>{{ $nicho->codigo }}
                    <span class="text-muted fw-normal ms-1">({{ $nicho->bloque->descripcion ?? 'N/A' }})</span>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold text-muted small text-uppercase mb-1">Socio Responsable</label>
                <div class="p-2 border rounded bg-light fw-bold text-dark">
                    <i class="fas fa-user text-warning me-2"></i>{{ $socioActual->apellidos ?? '' }} {{ $socioActual->nombres ?? '' }}
                </div>
            </div>
        </div>

        <div class="row g-3">
            {{-- BLOQUE: FALLECIDOS (Buscador) --}}
            <div class="col-12">
                <label class="form-label fw-bold text-primary">Fallecido a Inhumar <span class="text-danger">*</span></label>
                
                <input type="text" id="buscarFallecidoEdit" class="form-control mb-2 border-primary" 
                       placeholder="🔍 Escribe nombre o cédula para buscar..." autocomplete="off">
                
                <select name="fallecido_id" id="selectFallecidoEdit" class="form-select" required size="4">
                    @if($fallecidoActual)
                        <option value="{{ $fallecidoActual->id }}" selected>
                            {{ $fallecidoActual->apellidos }} {{ $fallecidoActual->nombres }} ({{ $fallecidoActual->cedula }})
                        </option>
                    @endif
                </select>
                <div class="mt-2">
                    <small class="text-muted">Seleccionado: <span id="fallecidoSeleccionadoEdit" class="fw-bold text-success">
                        {{ $fallecidoActual ? $fallecidoActual->apellidos.' '.$fallecidoActual->nombres : 'Ninguno' }}
                    </span></small>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small mb-1">Fecha Inhumación <span class="text-danger">*</span></label>
                <input type="date" name="fecha_inhumacion" 
                       value="{{ $fallecidoActual && $fallecidoActual->pivot->fecha_inhumacion ? $fallecidoActual->pivot->fecha_inhumacion->format('Y-m-d') : date('Y-m-d') }}" 
                       class="form-control border-primary" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold small mb-1">Observación</label>
                <input type="text" name="observacion" class="form-control border-primary"
                          placeholder="Nota corta..."
                          value="{{ $fallecidoActual && $fallecidoActual->pivot->observacion ? $fallecidoActual->pivot->observacion : '' }}">
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning fw-bold px-4">
            <i class="fas fa-save me-1"></i> Guardar Cambios
        </button>
    </div>
</form>

<script>
    (function() {
        var input = document.getElementById('buscarFallecidoEdit');
        var select = document.getElementById('selectFallecidoEdit');
        var label = document.getElementById('fallecidoSeleccionadoEdit');
        var includeId = "{{ $fallecidoActual->id ?? '' }}";
        var debounceTimer = null;

        function updateLabel() {
            var opt = select.options[select.selectedIndex];
            label.textContent = (opt && opt.value !== "") ? opt.text : "Ninguno";
        }

        function fetchData(q) {
            fetch('/api/asignaciones/fallecidos-disponibles?q=' + encodeURIComponent(q) + '&include_id=' + includeId)
                .then(r => r.json())
                .then(data => {
                    var currentVal = select.value;
                    select.innerHTML = '<option value="">-- Seleccionar --</option>';
                    data.forEach(item => {
                        var opt = document.createElement("option");
                        opt.value = item.id;
                        opt.textContent = item.apellidos + " " + item.nombres + " (" + item.cedula + ")";
                        if(String(item.id) === String(currentVal)) opt.selected = true;
                        select.appendChild(opt);
                    });
                    if (q.trim() !== '' && data.length > 0 && select.selectedIndex <= 0) {
                        select.selectedIndex = 1;
                        updateLabel();
                    }
                });
        }

        input.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchData(this.value), 300);
        });

        select.addEventListener('change', updateLabel);
    })();
</script>