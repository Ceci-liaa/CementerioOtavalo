<div class="modal-header bg-info text-white border-bottom-0 pb-0 py-2">
    <h5 class="modal-title fw-bold fs-6">Detalle del Fallecido</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body pt-2 pb-0">

    {{-- TARJETA DESTACADA --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-2 py-2 px-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">Código</small>
            <span class="fw-bold text-primary fs-5">{{ $fallecido->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">Cédula</small>
            <span class="fw-bold text-dark fs-5">{{ $fallecido->cedula ?? 'S/N' }}</span>
        </div>
    </div>

    {{-- TABS (2 PESTAÑAS) --}}
    <ul class="nav nav-tabs nav-fill mb-2" id="showTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold small py-1" data-bs-toggle="tab" data-bs-target="#info-tab">
                <i class="fas fa-user me-1"></i> Datos Personales
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold small py-1" data-bs-toggle="tab" data-bs-target="#detalles-tab">
                <i class="fas fa-book-dead me-1"></i> Detalles Fallecimiento
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- TAB 1: INFO PERSONAL --}}
        <div class="tab-pane fade show active" id="info-tab">
            <div class="row g-2">
                <div class="col-12">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Apellidos y Nombres</label>
                    <div class="fw-bold text-dark border-bottom pb-1 fs-6">
                        {{ $fallecido->apellidos }} {{ $fallecido->nombres }}
                    </div>
                </div>

                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Género</label>
                    <div class="text-dark small">{{ $fallecido->genero->nombre ?? '-' }}</div>
                </div>
                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Estado Civil</label>
                    <div class="text-dark small">{{ $fallecido->estadoCivil->nombre ?? '-' }}</div>
                </div>

                <div class="col-12 mt-2">
                    <div class="bg-light p-2 rounded">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            <div>
                                <small class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">Comunidad (Lugar)</small>
                                <span class="fw-bold text-dark small">{{ $fallecido->comunidad->nombre ?? 'No registrada' }}</span>
                                @if($fallecido->comunidad && $fallecido->comunidad->parroquia)
                                    <span class="text-muted text-xs">({{ $fallecido->comunidad->parroquia->nombre }})</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TAB 2: DETALLES, EDAD Y NOTAS --}}
        <div class="tab-pane fade" id="detalles-tab">
            <div class="row g-2 mt-1">
                {{-- Fechas --}}
                <div class="col-6">
                    <div class="p-2 border rounded text-center h-100">
                        <label class="d-block text-primary small fw-bold mb-1">Nacimiento</label>
                        <div class="text-dark">{{ optional($fallecido->fecha_nac)->format('d/m/Y') ?? '--' }}</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="p-2 border rounded text-center h-100 bg-light">
                        <label class="d-block text-danger small fw-bold mb-1">Fallecimiento</label>
                        <div class="text-dark fw-bold">{{ optional($fallecido->fecha_fallecimiento)->format('d/m/Y') ?? '--' }}</div>
                    </div>
                </div>
                
                {{-- CÁLCULO DE EDAD DETALLADA --}}
                <div class="col-12 text-center mt-2">
                    @if($fallecido->fecha_nac && $fallecido->fecha_fallecimiento)
                        @php
                            $edad = $fallecido->fecha_nac->diff($fallecido->fecha_fallecimiento);
                        @endphp
                        <div class="alert alert-secondary py-2 mb-0">
                            <small class="d-block text-uppercase text-muted" style="font-size: 0.65rem;">Edad Exacta</small>
                            <span class="fw-bold text-dark">
                                {{ $edad->y }} años, {{ $edad->m }} meses y {{ $edad->d }} días
                            </span>
                        </div>
                    @else
                        <span class="text-muted text-xs">Fechas incompletas para calcular edad</span>
                    @endif
                </div>

                {{-- Observaciones --}}
                <div class="col-12 mt-2">
                     <label class="d-block text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Notas / Observaciones</label>
                    @if($fallecido->observaciones)
                        <div class="bg-light p-2 rounded border text-sm text-secondary fst-italic">
                            "{{ $fallecido->observaciones }}"
                        </div>
                    @else
                        <div class="text-muted small border p-2 rounded text-center bg-light">
                            Sin observaciones registradas.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER AUDITORÍA --}}
    <div class="mt-2 pt-2 border-top d-flex justify-content-between text-xs text-muted pb-2">
        <span>Reg: {{ $fallecido->creador->name ?? 'Sistema' }}</span>
        <span>{{ $fallecido->created_at->format('d/m/Y H:i') }}</span>
    </div>
</div>

<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary w-100 btn-sm" data-bs-dismiss="modal">Cerrar</button>
</div>