<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nueva Asignación</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('asignaciones.store') }}">
    @csrf
    <div class="modal-body">
        @if ($errors->any()) 
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach</ul>
            </div> 
        @endif

        {{-- NAVEGACIÓN TABS --}}
        <ul class="nav nav-tabs nav-fill mb-3" id="asignacionTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#tabNicho" type="button">
                    <i class="fas fa-cube me-1"></i> 1. Nicho
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tabSocio" type="button">
                    <i class="fas fa-user me-1"></i> 2. Socio
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#tabFallecido" type="button">
                    <i class="fas fa-cross me-1"></i> 3. Fallecido
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            {{-- TAB 1: NICHO --}}
            <div class="tab-pane fade show active" id="tabNicho">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Nicho Disponible <span class="text-danger">*</span></label>
                        <input type="text" id="buscarNicho" class="form-control mb-2" placeholder="🔍 Buscar por código o bloque...">
                        <select name="nicho_id" id="selectNicho" class="form-select" required size="5" style="height: auto;">
                            <option value="">-- Seleccionar Nicho --</option>
                            @foreach($nichosDisponibles as $n)
                                <option value="{{ $n->id }}" 
                                    data-search="{{ strtolower($n->codigo . ' ' . optional($n->bloque)->nombre) }}">
                                    {{ $n->codigo }} - Bloque {{ optional($n->bloque)->nombre ?? 'N/A' }} 
                                    ({{ $n->fallecidos_count ?? 0 }}/{{ $n->capacidad }} Ocupados)
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Seleccionado: <span id="nichoSeleccionado" class="fw-bold text-primary">Ninguno</span></small>
                    </div>
                </div>
            </div>

            {{-- TAB 2: SOCIO --}}
            <div class="tab-pane fade" id="tabSocio">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Socio Responsable <span class="text-danger">*</span></label>
                        <input type="text" id="buscarSocio" class="form-control mb-2" placeholder="🔍 Buscar por nombre o cédula...">
                        <select name="socio_id" id="selectSocio" class="form-select" required size="5" style="height: auto;">
                            <option value="">-- Seleccionar Socio --</option>
                            @foreach($socios as $s)
                                <option value="{{ $s->id }}" 
                                    data-search="{{ strtolower($s->cedula . ' ' . $s->apellidos . ' ' . $s->nombres) }}">
                                    {{ $s->apellidos }} {{ $s->nombres }} ({{ $s->cedula }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Seleccionado: <span id="socioSeleccionado" class="fw-bold text-primary">Ninguno</span></small>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-bold">Rol del Socio</label>
                        <select name="rol" class="form-select">
                            <option value="TITULAR">TITULAR</option>
                            <option value="RESPONSABLE">RESPONSABLE</option>
                            <option value="CO-TITULAR">CO-TITULAR</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- TAB 3: FALLECIDO --}}
            <div class="tab-pane fade" id="tabFallecido">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Fallecido a Inhumar <span class="text-danger">*</span></label>
                        <input type="text" id="buscarFallecido" class="form-control mb-2" placeholder="🔍 Buscar por nombre o cédula...">
                        <select name="fallecido_id" id="selectFallecido" class="form-select" required size="5" style="height: auto;">
                            <option value="">-- Seleccionar Fallecido --</option>
                            @foreach($fallecidos as $f)
                                <option value="{{ $f->id }}" 
                                    data-search="{{ strtolower($f->cedula . ' ' . $f->apellidos . ' ' . $f->nombres) }}">
                                    {{ $f->apellidos }} {{ $f->nombres }} ({{ $f->cedula }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Seleccionado: <span id="fallecidoSeleccionado" class="fw-bold text-primary">Ninguno</span></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success fw-bold">Guardar</button> 
    </div>
</form>