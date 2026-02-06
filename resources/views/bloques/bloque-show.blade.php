{{-- CABECERA --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0 py-2">
    <h5 class="modal-title fw-bold fs-6">
        <i class="fa-solid fa-cube me-2"></i> Detalle del Bloque
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body pt-2 pb-0">
    
    {{-- TARJETA DESTACADA --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-2 py-2 px-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">Código</small>
            <span class="fw-bold text-primary fs-5">{{ $bloque->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">Área Total</small>
            <span class="fw-bold text-dark fs-5">{{ $bloque->area_m2 ? $bloque->area_m2 . ' m²' : '—' }}</span>
        </div>
    </div>

    {{-- TABS --}}
    <ul class="nav nav-tabs nav-fill mb-2" id="showTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold small py-1" data-bs-toggle="tab" data-bs-target="#info-tab">
                <i class="fas fa-info-circle me-1"></i> General
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold small py-1" data-bs-toggle="tab" data-bs-target="#geo-tab">
                <i class="fas fa-draw-polygon me-1"></i> Geometría
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        {{-- TAB 1: GENERAL --}}
        <div class="tab-pane fade show active" id="info-tab">
            <div class="row g-2 mt-1">
                <div class="col-12">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Nombre del Bloque</label>
                    <div class="fw-bold text-dark border-bottom pb-1 fs-6">
                        {{ $bloque->nombre }}
                    </div>
                </div>

                <div class="col-12 mt-2">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Descripción</label>
                    <div class="p-2 border rounded bg-light text-secondary text-xs">
                        {{ $bloque->descripcion ?: 'Sin descripción registrada.' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: GEOMETRÍA --}}
        <div class="tab-pane fade" id="geo-tab">
            <div class="row g-2 mt-1">
                <div class="col-12">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Polígono Origen (QGIS)</label>
                    <div class="fw-bold text-dark">
                        @if(optional($bloque->bloqueGeom)->codigo)
                            <i class="fas fa-map text-secondary me-1"></i> {{ $bloque->bloqueGeom->codigo }} - {{ $bloque->bloqueGeom->sector ?? 'General' }}
                        @else
                            <span class="text-muted fst-italic">No vinculado (Manual)</span>
                        @endif
                    </div>
                </div>

                {{-- CAMBIO: Color más oscuro (bg-dark) y ancho completo --}}
                <div class="col-12 mt-2">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Estado Mapa</label>
                    <div>
                        @if($bloque->geom) 
                            <span class="badge bg-success w-100 py-2"><i class="fas fa-check me-1"></i> GEOMETRÍA CARGADA</span> 
                        @else 
                            {{-- Se usa bg-dark para máximo contraste --}}
                            <span class="badge bg-dark w-100 py-2"><i class="fas fa-times me-1"></i> SIN GEOMETRÍA</span> 
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER AUDITORÍA --}}
    <div class="mt-2 pt-2 border-top d-flex justify-content-between text-xs text-muted pb-2">
        <span>Creado por: <strong>{{ $bloque->creador->name ?? 'Sistema' }}</strong></span>
        <span>{{ $bloque->created_at->format('d/m/Y H:i') }}</span>
    </div>
</div>

<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary w-100 btn-sm" data-bs-dismiss="modal">Cerrar</button>
</div>