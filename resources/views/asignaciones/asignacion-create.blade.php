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
                        <input type="text" id="buscarNichoData" class="form-control mb-2" placeholder="🔍 Buscar por código o bloque...">
                        <select name="nicho_id" id="selectNichoData" class="form-select" required size="5" style="height: auto;">
                            <!-- Opciones cargadas por AJAX -->
                        </select>
                        <small class="text-muted">Seleccionado: <span id="nichoSeleccionadoData" class="fw-bold text-primary">Ninguno</span></small>
                    </div>
                </div>
            </div>

            {{-- TAB 2: SOCIO --}}
            <div class="tab-pane fade" id="tabSocio">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold">Socio Responsable <span class="text-danger">*</span></label>
                        <input type="text" id="buscarSocioData" class="form-control mb-2" placeholder="🔍 Buscar por nombre o cédula...">
                        <select name="socio_id" id="selectSocioData" class="form-select" required size="5" style="height: auto;">
                            <!-- Opciones cargadas por AJAX -->
                        </select>
                        <small class="text-muted">Seleccionado: <span id="socioSeleccionadoData" class="fw-bold text-primary">Ninguno</span></small>
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
                        <input type="text" id="buscarFallecidoData" class="form-control mb-2" placeholder="🔍 Buscar por nombre o cédula...">
                        <select name="fallecido_id" id="selectFallecidoData" class="form-select" required size="5" style="height: auto;">
                            <!-- Opciones cargadas por AJAX -->
                        </select>
                        <small class="text-muted">Seleccionado: <span id="fallecidoSeleccionadoData" class="fw-bold text-primary">Ninguno</span></small>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-bold">Fecha de Inhumación <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_inhumacion" class="form-control" required value="{{ date('Y-m-d') }}">
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

<script>
    (function() {
        function setupAjaxSearch(inputId, selectId, labelId, apiUrl, renderCallback) {
            var input = document.getElementById(inputId);
            var select = document.getElementById(selectId);
            var label = document.getElementById(labelId);
            if (!input || !select || !label) return;

            var debounceTimer = null;

            function updateLabel() {
                var opt = select.options[select.selectedIndex];
                if (opt && opt.value !== "") {
                    label.textContent = opt.text;
                    label.className = "fw-bold text-success";
                } else {
                    label.textContent = "Ninguno";
                    label.className = "fw-bold text-primary";
                }
            }

            function fetchData(q) {
                fetch(apiUrl + '?q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        select.innerHTML = '<option value="">-- Seleccionar --</option>';
                        data.forEach(function(item) {
                            var opt = document.createElement("option");
                            renderCallback(opt, item);
                            select.appendChild(opt);
                        });
                        
                        input.classList.remove('search-input-found', 'search-input-empty');
                        if (q.trim() !== '') {
                            if (data.length > 0) {
                                input.classList.add('search-input-found');
                                if (select.options.length > 1) { select.selectedIndex = 1; updateLabel(); }
                            } else {
                                input.classList.add('search-input-empty');
                            }
                        }
                    });
            }

            // Inicializar con vacío
            fetchData('');

            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                var val = this.value;
                debounceTimer = setTimeout(function() { fetchData(val); }, 300);
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); e.stopPropagation();
                    var opt = select.options[select.selectedIndex];
                    if (opt && opt.value !== '') {
                        var text = opt.text;
                        var val = opt.value;
                        input.value = '';
                        input.classList.remove('search-input-found', 'search-input-empty');
                        
                        // Refetch all and preserve selection
                        fetch(apiUrl + '?q=')
                            .then(function(r) { return r.json(); })
                            .then(function(data) {
                                select.innerHTML = '<option value="">-- Seleccionar --</option>';
                                data.forEach(function(item) {
                                    var newOpt = document.createElement("option");
                                    renderCallback(newOpt, item);
                                    if(String(newOpt.value) === String(val)) newOpt.selected = true;
                                    select.appendChild(newOpt);
                                });
                                updateLabel();
                            });

                        input.placeholder = '✅ ' + text.substring(0, 35);
                        input.style.background = '#d4edda';
                        var inputRef = input;
                        setTimeout(function() { inputRef.placeholder = '🔍 Buscar...'; inputRef.style.background = ''; }, 1500);
                    }
                    return false;
                }
                if (e.key === 'ArrowDown') { e.preventDefault(); if (select.selectedIndex < select.options.length - 1) { select.selectedIndex++; updateLabel(); } }
                if (e.key === 'ArrowUp') { e.preventDefault(); if (select.selectedIndex > 0) { select.selectedIndex--; updateLabel(); } }
            });

            select.addEventListener('change', updateLabel);
            select.addEventListener('click', updateLabel);
        }

        // Búsqueda de Nichos Disponibles
        setupAjaxSearch('buscarNichoData', 'selectNichoData', 'nichoSeleccionadoData', '/api/asignaciones/nichos-disponibles', function(opt, item) {
            opt.value = item.id;
            opt.textContent = item.codigo + " - Bloque " + item.bloque_nombre + " (" + item.ocupados + "/" + item.capacidad + " Ocupados)";
        });

        // Búsqueda de Socios (reutilizamos la ruta del nicho que ya busca socios)
        setupAjaxSearch('buscarSocioData', 'selectSocioData', 'socioSeleccionadoData', '/api/socios/search', function(opt, item) {
            opt.value = item.id;
            opt.textContent = item.apellidos + " " + item.nombres + " (" + item.cedula + ")";
        });

        // Búsqueda de Fallecidos Disponibles
        setupAjaxSearch('buscarFallecidoData', 'selectFallecidoData', 'fallecidoSeleccionadoData', '/api/asignaciones/fallecidos-disponibles', function(opt, item) {
            opt.value = item.id;
            opt.textContent = item.apellidos + " " + item.nombres + " (" + item.cedula + ")";
        });
    })();
</script>