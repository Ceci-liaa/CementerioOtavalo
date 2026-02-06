{{-- LIBRERÍAS (TomSelect) --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<style>
    /* ESTILOS TOM-SELECT */
    .ts-wrapper .ts-control { border: 1px solid #dee2e6 !important; background-color: #fff !important; border-radius: 0.375rem !important; padding: 0.5rem 0.75rem !important; min-height: 40px !important; font-size: 1rem !important; box-shadow: none !important; display: flex; align-items: center; }
    .ts-wrapper.focus .ts-control { border-color: #5ea6f7 !important; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25) !important; outline: 0 !important; }
    .ts-dropdown { z-index: 99999 !important; border-color: #5ea6f7 !important; border-radius: 0.375rem !important; margin-top: 4px !important; }
    .ts-dropdown .option { padding: 10px 15px !important; font-size: 0.9rem !important; }
    .ts-dropdown .active { background-color: #e7f1ff !important; color: #1c2a48 !important; font-weight: 600 !important; }
    .ts-wrapper.single .ts-control::after { display: none !important; }
</style>

{{-- CABECERA --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">
        <i class="fa-solid fa-pen-to-square me-2"></i> Editar Bloque
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('bloques.update', $bloque) }}">
    @csrf @method('PUT')
    
    <div class="modal-body">
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- TABS --}}
        <ul class="nav nav-tabs nav-fill mb-3" id="editTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-datos" type="button">
                    <i class="fas fa-file-signature me-1"></i> Datos Principales
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-gis" type="button">
                    <i class="fas fa-map-marked-alt me-1"></i> Ubicación GIS
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            {{-- TAB 1: DATOS --}}
            <div class="tab-pane fade show active" id="edit-datos">
                <div class="row g-3">
                    <div class="col-12">
                         <label class="form-label fw-bold text-muted small">Código Actual</label>
                         <input name="codigo" value="{{ old('codigo', $bloque->codigo) }}" class="form-control bg-light" readonly>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label fw-bold small">Nombre del Bloque <span class="text-danger">*</span></label>
                        <input name="nombre" value="{{ old('nombre', $bloque->nombre) }}" class="form-control" required>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Área (m²)</label>
                        <input type="number" step="0.01" min="0" name="area_m2" value="{{ old('area_m2', $bloque->area_m2) }}" class="form-control">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $bloque->descripcion) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- TAB 2: GIS --}}
            <div class="tab-pane fade" id="edit-gis">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-primary small">Vincular con Código GIS</label>
                        <select id="bloque_geom_id_edit" name="bloque_geom_id" placeholder="Buscar código...">
                            <option value="">-- Sin Asignación (Manual) --</option>
                            @isset($bloquesGeom)
                                @foreach($bloquesGeom as $bg)
                                    <option value="{{ $bg->id }}" @selected(old('bloque_geom_id', $bloque->bloque_geom_id) == $bg->id)>
                                        {{ $bg->codigo }} | {{ $bg->sector ?? 'General' }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        <small class="text-muted text-xs mt-1 d-block">
                            <i class="fas fa-exclamation-triangle me-1"></i> Si cambias esto, el código del bloque se actualizará.
                        </small>
                    </div>

                    {{-- Input Oculto para JSON --}}
                    <input type="hidden" id="geom" name="geom" value="{{ old('geom', $bloque->geom ? json_encode($bloque->geom) : '') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning fw-bold px-4">Actualizar Bloque</button>
    </div>
</form>

<script>
    var settingsEdit = { create: false, sortField: { field: "text", direction: "asc" }, plugins: ['dropdown_input'] };
    // Usamos un ID diferente (_edit) para evitar conflictos si se cargan scripts múltiples veces
    new TomSelect("#bloque_geom_id_edit", settingsEdit);
</script>

