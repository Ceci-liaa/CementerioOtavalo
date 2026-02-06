{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-info text-white border-bottom-0 pb-0 py-2">
    <h5 class="modal-title fw-bold fs-6">Detalle del Socio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body pt-2 pb-0">

    {{-- TARJETA DESTACADA COMPACTA (Menos padding py-2) --}}
    <div class="alert alert-light border d-flex justify-content-between align-items-center mb-2 py-2 px-3 shadow-sm">
        <div>
            <small class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">Código Socio</small>
            <span class="fw-bold text-primary fs-5">{{ $socio->codigo }}</span>
        </div>
        <div class="text-end border-start ps-3">
            <small class="d-block text-muted text-uppercase" style="font-size: 0.65rem;">Cédula</small>
            <span class="fw-bold text-dark fs-5">{{ $socio->cedula }}</span>
        </div>
    </div>

    {{-- NAVEGACIÓN TABS --}}
    <ul class="nav nav-tabs nav-fill mb-2" id="showTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active fw-bold small py-1" data-bs-toggle="tab" data-bs-target="#info-tab">
                <i class="fas fa-user me-1"></i> Datos
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold small py-1" data-bs-toggle="tab" data-bs-target="#membresia-tab">
                <i class="fas fa-file-contract me-1"></i> Estado
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link fw-bold small py-1" data-bs-toggle="tab" data-bs-target="#nichos-tab">
                <i class="fas fa-monument me-1"></i> Nichos
                {{-- Cambié 'bg-dark text-white' por 'bg-light text-dark' y agregué borde --}}
                <span class="badge bg-light text-dark border ms-1">{{ $socio->nichos->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- TAB 1: INFORMACIÓN PERSONAL (Compactado) --}}
        <div class="tab-pane fade show active" id="info-tab">
            <div class="row g-2"> {{-- g-2 reduce el espacio entre elementos --}}

                {{-- Fila 1: Nombre completo --}}
                <div class="col-12">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Socio</label>
                    <div class="fw-bold text-dark border-bottom pb-1">{{ $socio->apellidos }} {{ $socio->nombres }}
                    </div>
                </div>

                {{-- Fila 2: 3 Columnas para ahorrar espacio vertical --}}
                <div class="col-4">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Edad</label>
                    <div class="fw-bold text-dark small">{{ $socio->edad }} años</div>
                </div>
                <div class="col-4">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Nacimiento</label>
                    <div class="text-dark small">{{ optional($socio->fecha_nac)->format('d/m/Y') }}</div>
                </div>
                <div class="col-4">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Civil</label>
                    <div class="text-dark small">{{ $socio->estadoCivil?->nombre ?? '-' }}</div>
                </div>

                {{-- Fila 3: Ubicación fusionada --}}
                <div class="col-12 mt-2">
                    <div class="bg-light p-2 rounded">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-map-marker-alt text-danger me-2"></i>
                            <div>
                                <span class="fw-bold text-dark small">{{ $socio->comunidad?->nombre }}</span>
                                <span
                                    class="text-muted text-xs ms-1">({{ $socio->comunidad?->parroquia?->nombre }})</span>
                            </div>
                        </div>
                        <div class="text-muted text-xs text-truncate ps-4">
                            {{ $socio->direccion ?? 'Sin dirección registrada' }}
                        </div>
                    </div>
                </div>

                {{-- Fila 4: Contacto --}}
                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Teléfono</label>
                    <div class="text-dark small fw-bold">{{ $socio->telefono ?? '-' }}</div>
                </div>
                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Email</label>
                    <div class="text-dark small text-truncate">{{ $socio->email ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- TAB 2: ESTADO Y BENEFICIOS (Colores corregidos) --}}
        <div class="tab-pane fade" id="membresia-tab">
            <div class="row g-3 mt-1 align-items-center">

                {{-- ESTATUS: Color sólido y oscuro --}}
                <div class="col-6 text-center">
                    <label class="d-block text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Estatus</label>
                    @if($socio->estatus == 'vivo')
                        <div class="badge w-100 py-2" style="background-color: #198754; color: white; font-size: 0.85rem;">
                            VIVO
                        </div>
                    @else
                        <div class="badge bg-dark w-100 py-2" style="color: white; font-size: 0.85rem;">
                            FALLECIDO
                        </div>
                    @endif
                </div>

                {{-- BENEFICIO --}}
                <div class="col-6 text-center">
                    <label class="d-block text-muted text-uppercase mb-1" style="font-size: 0.65rem;">Beneficio</label>
                    @if($socio->tipo_beneficio == 'exonerado')
                        {{-- Agregué 'text-dark' para contraste --}}
                        <span class="badge bg-success text-dark w-100 py-2">EXONERADO</span>
                    @elseif($socio->tipo_beneficio == 'con_subsidio')
                        {{-- Agregué 'text-dark' para contraste --}}
                        <span class="badge bg-primary text-dark w-100 py-2">CON SUBSIDIO</span>
                    @else
                        {{-- Agregué 'text-dark' para contraste --}}
                        <span class="badge bg-secondary text-dark w-100 py-2">SIN SUBSIDIO</span>
                    @endif
                </div>

                <div class="col-12">
                    <hr class="my-1 opacity-25">
                </div>

                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0" style="font-size: 0.65rem;">Condición</label>
                    <div class="fw-bold text-dark small text-capitalize">{{ str_replace('_', ' ', $socio->condicion) }}
                    </div>
                </div>

                <div class="col-6">
                    <label class="d-block text-muted text-uppercase mb-0"
                        style="font-size: 0.65rem;">Inscripción</label>
                    <div class="text-dark small">{{ optional($socio->fecha_inscripcion)->format('d/m/Y') }}</div>
                </div>

                @if($socio->fecha_exoneracion)
                    <div class="col-12">
                        <div class="alert alert-success py-1 px-2 mb-0 text-xs text-center">
                            Exonerado desde: <strong>{{ $socio->fecha_exoneracion->format('d/m/Y') }}</strong>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- TAB 3: NICHOS (Tabla sin cambios, solo contenedor) --}}
        <div class="tab-pane fade" id="nichos-tab">
            @if($socio->nichos->isEmpty())
                <div class="text-center py-4 text-muted">
                    <p class="small mb-0">No tiene nichos asignados.</p>
                </div>
            @else
                <div class="table-responsive border rounded" style="max-height: 200px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0 text-xs align-middle">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th class="ps-3">Código</th>
                                <th>Tipo</th>
                                <th>Ubicación</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($socio->nichos as $nicho)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $nicho->codigo }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $nicho->tipo_nicho === 'PROPIO' ? 'bg-warning text-dark' : 'bg-info text-dark' }}">
                                            {{ substr($nicho->tipo_nicho, 0, 4) }}
                                        </span>
                                    </td>
                                    <td>{{ $nicho->bloque ? $nicho->bloque->codigo : '-' }}</td>
                                    <td class="text-center">
                                        @if($nicho->disponible)
                                            <i class="fas fa-circle text-success" title="Disponible"></i>
                                        @else
                                            <i class="fas fa-circle text-danger" title="Ocupado"></i>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- FOOTER AUDITORÍA COMPACTO --}}
    <div class="mt-2 pt-2 border-top d-flex justify-content-between text-xs text-muted pb-2">
        <span>Reg: {{ $socio->creador?->name ?? 'Sistema' }}</span>
        <span>{{ $socio->created_at ? $socio->created_at->format('d/m/Y H:i') : '' }}</span>
    </div>
</div>

<div class="modal-footer border-top-0 pt-0">
    <button type="button" class="btn btn-secondary w-100 btn-sm" data-bs-dismiss="modal">Cerrar</button>
</div>