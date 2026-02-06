{{-- LIBRERÍAS (TomSelect) --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<style>
    /* ESTILOS TOM-SELECT (Idénticos a Nichos) */
    .ts-wrapper .ts-control { border: 1px solid #dee2e6 !important; background-color: #fff !important; border-radius: 0.375rem !important; padding: 0.5rem 0.75rem !important; min-height: 40px !important; font-size: 1rem !important; box-shadow: none !important; display: flex; align-items: center; }
    .ts-wrapper.focus .ts-control { border-color: #5ea6f7 !important; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25) !important; outline: 0 !important; }
    .ts-dropdown { z-index: 99999 !important; border-color: #5ea6f7 !important; border-radius: 0.375rem !important; margin-top: 4px !important; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
    .ts-dropdown .option { padding: 10px 15px !important; font-size: 0.9rem !important; }
    .ts-dropdown .active { background-color: #e7f1ff !important; color: #1c2a48 !important; font-weight: 600 !important; }
    .ts-wrapper.single .ts-control::after { display: none !important; }
</style>

{{-- CABECERA --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">
        <i class="fa-solid fa-layer-group me-2"></i> Nuevo Bloque
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('bloques.store') }}">
    @csrf
    
    <div class="modal-body">
        
        {{-- Mensaje Informativo --}}
        <div class="alert alert-info py-2 mb-3 text-xs shadow-sm border-0" style="background-color: #e7f1ff; color: #0c5460;">
            <i class="fas fa-info-circle me-1"></i> Si seleccionas un <b>Código GIS</b>, este se asignará automáticamente.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- TABS --}}
        <ul class="nav nav-tabs nav-fill mb-3" id="createTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold small" data-bs-toggle="tab" data-bs-target="#datos" type="button">
                    <i class="fas fa-file-signature me-1"></i> Datos Principales
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#gis" type="button">
                    <i class="fas fa-map-marked-alt me-1"></i> Ubicación GIS
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            {{-- TAB 1: DATOS --}}
            <div class="tab-pane fade show active" id="datos">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold small">Nombre del Bloque <span class="text-danger">*</span></label>
                        <input name="nombre" value="{{ old('nombre') }}" class="form-control" required placeholder="Ej. Bloque Norte">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Área (m²)</label>
                        <input type="number" step="0.01" min="0" name="area_m2" value="{{ old('area_m2') }}" class="form-control" placeholder="0.00">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- TAB 2: GIS --}}
            <div class="tab-pane fade" id="gis">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-primary small">Código GIS y Sector (Mapa)</label>
                        <select id="bloque_geom_id" name="bloque_geom_id" placeholder="Buscar código en mapa...">
                            <option value="">-- Seleccionar Código del Mapa --</option>
                            @isset($bloquesGeom)
                                @foreach($bloquesGeom as $bg)
                                    <option value="{{ $bg->id }}" @selected(old('bloque_geom_id') == $bg->id)>
                                        {{ $bg->codigo }} | {{ $bg->sector ?? 'General' }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        <small class="text-muted text-xs mt-1 d-block">
                            <i class="fas fa-arrow-up me-1"></i> El código seleccionado (ej. B-20) será el código del nuevo bloque.
                        </small>
                    </div>

                    {{-- Input Oculto para JSON (Se mantiene por tu lógica de JS) --}}
                    <input type="hidden" id="geom" name="geom" value="{{ old('geom') }}">
                </div>
            </div>

        </div>
    </div>

    {{-- PIE DEL MODAL --}}
    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success fw-bold px-4">Guardar Bloque</button>
    </div>
</form>

<script>
    // Inicializar TomSelect
    var settings = {
        create: false,
        sortField: { field: "text", direction: "asc" },
        plugins: ['dropdown_input'],
    };
    new TomSelect("#bloque_geom_id", settings);
</script>