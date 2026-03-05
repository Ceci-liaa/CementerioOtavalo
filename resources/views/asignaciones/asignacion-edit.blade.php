<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Corregir Datos de Asignación</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('asignaciones.update', $nicho->identificacion) }}">
    @csrf @method('PUT')

    @php
        $socioActual = $nicho->socios->first();
        $fallecidoActual = $nicho->fallecidos->where('pivot.fecha_exhumacion', null)->first();
    @endphp

    {{-- ID del fallecido que está actualmente para que el controlador sepa a quién reemplazar --}}
    <input type="hidden" name="fallecido_anterior_id" value="{{ $fallecidoActual->id ?? '' }}">

    <div class="modal-body">
        <div class="alert alert-warning py-2 mb-3 text-xs">
            <i class="fas fa-exclamation-triangle me-1"></i> Use esto para corregir errores de dedo. El Socio y el Nicho son fijos.
        </div>

        <div class="row g-3">
            {{-- BLOQUE 1: DATOS FIJOS (Solo Lectura) --}}
            <div class="col-md-6">
                <label class="form-label fw-bold text-muted small text-uppercase">Nicho</label>
                <div class="form-control bg-light border-dashed fw-bold">{{ $nicho->codigo }}</div>
                <small class="text-secondary">Bloque: {{ $nicho->bloque->descripcion ?? 'N/A' }}</small>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold text-muted small text-uppercase">Socio Responsable</label>
                <div class="form-control bg-light border-dashed fw-bold">{{ $socioActual->apellidos ?? '' }} {{ $socioActual->nombres ?? '' }}</div>
                <small class="text-secondary">Rol: {{ $socioActual->pivot->rol ?? 'N/A' }}</small>
            </div>

            <div class="col-12"><hr class="my-2 opacity-25"></div>

            {{-- BLOQUE 2: FALLECIDOS (Buscador) --}}
            <div class="col-12">
                <label class="form-label fw-bold text-primary">Fallecido a Inhumar <span class="text-danger">*</span></label>
                
                {{-- Input de búsqueda (Igual que en Create) --}}
                <input type="text" id="buscarFallecidoEdit" class="form-control mb-2 border-primary" 
                       placeholder="🔍 Buscar por nombre o cédula para cambiar..." autocomplete="off">
                
                <select name="fallecido_id" id="selectFallecidoEdit" class="form-select" required size="4">
                    @if($fallecidoActual)
                        <option value="{{ $fallecidoActual->id }}" selected>
                            {{ $fallecidoActual->apellidos }} {{ $fallecidoActual->nombres }} ({{ $fallecidoActual->cedula }})
                        </option>
                    @endif
                </select>
                <div class="mt-1">
                    <small class="text-muted">Seleccionado: <span id="fallecidoSeleccionadoEdit" class="fw-bold text-success">
                        {{ $fallecidoActual ? $fallecidoActual->apellidos.' '.$fallecidoActual->nombres : 'Ninguno' }}
                    </span></small>
                </div>
            </div>

            <div class="col-md-6 mt-3">
                <label class="form-label fw-bold">Fecha Inhumación <span class="text-danger">*</span></label>
                <input type="date" name="fecha_inhumacion" 
                       value="{{ $fallecidoActual && $fallecidoActual->pivot->fecha_inhumacion ? $fallecidoActual->pivot->fecha_inhumacion->format('Y-m-d') : date('Y-m-d') }}" 
                       class="form-control border-primary" required>
            </div>

            <div class="col-md-12 mt-3">
                <label class="form-label fw-bold">Observación</label>
                <textarea name="observacion" class="form-control" rows="2" 
                          placeholder="Motivo de la corrección...">{{ $fallecidoActual && $fallecidoActual->pivot->observacion ? $fallecidoActual->pivot->observacion : '' }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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