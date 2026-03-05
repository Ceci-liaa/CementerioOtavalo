<style>
    /* Estilos de búsqueda (idénticos a create) */
    .search-match { background: linear-gradient(90deg, #d4edda 0%, #c3e6cb 100%) !important; font-weight: 600 !important; }
    .search-first-match { background: linear-gradient(90deg, #28a745 0%, #20c997 100%) !important; color: white !important; font-weight: 700 !important; }
    .search-input-found { border: 2px solid #28a745 !important; box-shadow: 0 0 8px rgba(40, 167, 69, 0.4) !important; }
    .search-input-empty { border: 2px solid #dc3545 !important; box-shadow: 0 0 8px rgba(220, 53, 69, 0.4) !important; }
</style>

<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">
        <i class="fa-solid fa-pen-to-square me-2"></i> Editar Nicho
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<form method="POST" action="{{ route('nichos.update', $nicho) }}">
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
                <button class="nav-link active fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-ubicacion" type="button">
                    <i class="fas fa-map-marker-alt me-1"></i> Ubicación
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-caracteristicas" type="button">
                    <i class="fas fa-cogs me-1"></i> Datos Técnicos
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- TAB 1: UBICACIÓN --}}
            <div class="tab-pane fade show active" id="edit-ubicacion">
                <div class="row g-3">

                    {{-- Código Actual (solo lectura) --}}
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small">Código Actual</label>
                        <input value="{{ $nicho->codigo }}" class="form-control bg-light" readonly>
                    </div>

                    {{-- GIS (Buscable) --}}
                    <div class="col-12">
                        <label class="form-label fw-bold text-primary small">Mapa GIS (Opcional)</label>
                        <input type="text" id="buscarGisEdit" class="form-control mb-2" placeholder="🔍 Buscar código en mapa...">
                        <select name="nicho_geom_id" id="selectGisEdit" class="form-select" size="2" style="height: auto;">
                            <option value="">-- Ninguno (Manual) --</option>
                            @isset($nichosGeom)
                                @foreach($nichosGeom as $ng)
                                    <option value="{{ $ng->id }}" 
                                        data-search="{{ strtolower($ng->codigo) }}"
                                        @selected(old('nicho_geom_id', $nicho->nicho_geom_id) == $ng->id)>{{ $ng->codigo }}</option>
                                @endforeach
                            @endisset
                        </select>
                        <small class="text-muted">Seleccionado: <span id="gisSeleccionadoEdit" class="fw-bold text-primary">Ninguno</span></small>
                    </div>

                    {{-- Bloque (Buscable) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Bloque <span class="text-danger">*</span></label>
                        <input type="text" id="buscarBloqueEdit" class="form-control mb-2" placeholder="🔍 Buscar bloque...">
                        <select name="bloque_id" id="selectBloqueEdit" class="form-select" required size="2" style="height: auto;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($bloques as $b)
                                <option value="{{ $b->identificacion }}" 
                                    data-search="{{ strtolower($b->nombre . ' ' . ($b->codigo ?? '')) }}"
                                    @selected(old('bloque_id', $nicho->bloque_id) == $b->identificacion)>{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Seleccionado: <span id="bloqueSeleccionadoEdit" class="fw-bold text-primary">Ninguno</span></small>
                    </div>
                    
                    {{-- Socio (Buscable) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Socio Titular <span class="text-danger">*</span></label>
                        <input type="text" id="buscarSocioEdit" class="form-control mb-2" placeholder="🔍 Buscar por nombre o cédula...">
                        <select name="socio_id" id="selectSocioEdit" class="form-select" required size="2" style="height: auto;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($socios as $s)
                                <option value="{{ $s->id }}" 
                                    data-search="{{ strtolower($s->cedula . ' ' . $s->apellidos . ' ' . $s->nombres) }}"
                                    @selected(old('socio_id', $nicho->socio_id) == $s->id)>{{ $s->apellidos }} {{ $s->nombres }} ({{ $s->cedula }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Seleccionado: <span id="socioSeleccionadoEdit" class="fw-bold text-primary">Ninguno</span></small>
                    </div>
                </div>
            </div>

            {{-- TAB 2: DATOS TÉCNICOS --}}
            <div class="tab-pane fade" id="edit-caracteristicas">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tipo</label>
                        <select name="tipo_nicho" class="form-select" required>
                            <option value="PROPIO" @selected($nicho->tipo_nicho == 'PROPIO')>PROPIO</option>
                            <option value="COMPARTIDO" @selected($nicho->tipo_nicho == 'COMPARTIDO')>COMPARTIDO</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Clase</label>
                        <select name="clase_nicho" class="form-select" required>
                            <option value="BOVEDA" @selected($nicho->clase_nicho == 'BOVEDA')>Bóveda</option>
                            <option value="TIERRA" @selected($nicho->clase_nicho == 'TIERRA')>Tierra</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Capacidad</label>
                        <input type="number" min="1" name="capacidad" value="{{ old('capacidad', $nicho->capacidad) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Estado</label>
                        <select name="estado" class="form-select" required>
                            @foreach(['BUENO','MANTENIMIENTO','MALO','ABANDONADO'] as $e)
                                <option value="{{ $e }}" @selected($nicho->estado == $e)>{{ ucfirst(strtolower($e)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold small">Notas</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion', $nicho->descripcion) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning fw-bold px-4">Actualizar</button>
    </div>
</form>

<script>
    // ========== FILTRO EN CASCADA: Bloque → Nichos GIS (Edit) ==========
    (function() {
        var currentNichoId = {{ $nicho->identificacion }};
        var currentGeomId = {{ $nicho->nicho_geom_id ?? 'null' }};

        var selectBloque = document.getElementById('selectBloqueEdit');
        var selectGis = document.getElementById('selectGisEdit');
        var buscarGis = document.getElementById('buscarGisEdit');
        var gisSeleccionado = document.getElementById('gisSeleccionadoEdit');

        var gisOptions = [];

        function updateGisFromApi(bloqueId) {
            if (!bloqueId) {
                selectGis.innerHTML = '<option value="">-- Seleccione un bloque primero --</option>';
                gisOptions = [];
                if (gisSeleccionado) {
                    gisSeleccionado.textContent = 'Ninguno';
                    gisSeleccionado.className = 'fw-bold text-primary';
                }
                return;
            }

            selectGis.innerHTML = '<option value="">⏳ Cargando...</option>';

            fetch('/api/nichos-geom-por-bloque?bloque_id=' + bloqueId + '&exclude_nicho_id=' + currentNichoId)
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    gisOptions = [];
                    selectGis.innerHTML = '<option value="">-- Ninguno (Manual) --</option>';

                    if (data.length === 0) {
                        var opt = document.createElement('option');
                        opt.disabled = true;
                        opt.textContent = '⚠️ No hay nichos GIS disponibles para este bloque';
                        selectGis.appendChild(opt);
                    }

                    data.forEach(function(ng) {
                        var opt = document.createElement('option');
                        opt.value = ng.id;
                        opt.textContent = ng.codigo;
                        opt.setAttribute('data-search', (ng.codigo || '').toLowerCase());
                        if (currentGeomId && ng.id == currentGeomId) {
                            opt.selected = true;
                        }
                        selectGis.appendChild(opt);
                        gisOptions.push({
                            value: String(ng.id),
                            text: ng.codigo,
                            searchData: (ng.codigo || '').toLowerCase()
                        });
                    });

                    // Actualizar label
                    if (gisSeleccionado) {
                        var selectedOpt = selectGis.options[selectGis.selectedIndex];
                        if (selectedOpt && selectedOpt.value !== '') {
                            gisSeleccionado.textContent = selectedOpt.text;
                            gisSeleccionado.className = 'fw-bold text-success';
                        } else {
                            gisSeleccionado.textContent = 'Ninguno';
                            gisSeleccionado.className = 'fw-bold text-primary';
                        }
                    }
                })
                .catch(function() {
                    selectGis.innerHTML = '<option value="">-- Error al cargar --</option>';
                });
        }

        // Escuchar cambio en el select de Bloque
        if (selectBloque) {
            selectBloque.addEventListener('change', function() {
                updateGisFromApi(this.value);
            });
        }

        // Carga inicial con el bloque actual del nicho
        if (selectBloque && selectBloque.value) {
            updateGisFromApi(selectBloque.value);
        }
    })();

    // ========== BÚSQUEDA PARA SELECTS (Edit) ==========
    (function() {
        var configs = [
            { inputId: 'buscarGisEdit', selectId: 'selectGisEdit', labelId: 'gisSeleccionadoEdit' },
            { inputId: 'buscarBloqueEdit', selectId: 'selectBloqueEdit', labelId: 'bloqueSeleccionadoEdit' },
            { inputId: 'buscarSocioEdit', selectId: 'selectSocioEdit', labelId: 'socioSeleccionadoEdit' }
        ];

        configs.forEach(function(config) {
            var input = document.getElementById(config.inputId);
            var select = document.getElementById(config.selectId);
            var label = document.getElementById(config.labelId);
            
            if (!input || !select) return;
            
            // Guardar opciones originales
            var allOptions = [];
            function captureOptions() {
                allOptions = [];
                Array.from(select.options).forEach(function(opt) {
                    allOptions.push({
                        value: opt.value,
                        text: opt.text,
                        selected: opt.selected,
                        disabled: opt.disabled,
                        searchData: (opt.getAttribute('data-search') || opt.text).toLowerCase()
                    });
                });
            }
            captureOptions();

            // Re-capturar opciones cuando el select cambia dinámicamente
            var observer = new MutationObserver(function() { captureOptions(); });
            observer.observe(select, { childList: true });
            
            function updateOptions(searchTerm) {
                var term = searchTerm.toLowerCase().trim();
                select.innerHTML = '';
                var matchCount = 0;
                var firstMatchIndex = -1;
                
                allOptions.forEach(function(optData) {
                    var matches = optData.value === '' || 
                                   optData.searchData.includes(term) || 
                                   optData.text.toLowerCase().includes(term);
                    
                    if (term === '' || matches) {
                        var option = document.createElement('option');
                        option.value = optData.value;
                        option.textContent = optData.text;
                        if (optData.disabled) option.disabled = true;
                        
                        if (term !== '' && optData.value !== '' && !optData.disabled && matches) {
                            matchCount++;
                            if (firstMatchIndex === -1) {
                                firstMatchIndex = select.options.length;
                                option.className = 'search-first-match';
                            } else {
                                option.className = 'search-match';
                            }
                        }
                        select.appendChild(option);
                    }
                });
                
                input.classList.remove('search-input-found', 'search-input-empty');
                if (term !== '') {
                    if (matchCount > 0) {
                        input.classList.add('search-input-found');
                        if (firstMatchIndex !== -1) {
                            select.selectedIndex = firstMatchIndex;
                            updateLabel();
                        }
                    } else {
                        input.classList.add('search-input-empty');
                    }
                }
            }
            
            function updateLabel() {
                if (!label) return;
                var selectedOpt = select.options[select.selectedIndex];
                if (selectedOpt && selectedOpt.value !== '') {
                    label.textContent = selectedOpt.text;
                    label.classList.remove('text-primary');
                    label.classList.add('text-success');
                } else {
                    label.textContent = 'Ninguno';
                    label.classList.remove('text-success');
                    label.classList.add('text-primary');
                }
            }
            
            // Evento: escribir
            input.addEventListener('input', function() {
                updateOptions(this.value);
            });
            
            // Evento: teclas
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var selectedOpt = select.options[select.selectedIndex];
                    if (selectedOpt && selectedOpt.value !== '') {
                        var selectedValue = selectedOpt.value;
                        var selectedText = selectedOpt.text;
                        
                        this.value = '';
                        this.classList.remove('search-input-found', 'search-input-empty');
                        updateOptions('');
                        
                        for (var i = 0; i < select.options.length; i++) {
                            if (select.options[i].value === selectedValue) {
                                select.selectedIndex = i;
                                break;
                            }
                        }
                        updateLabel();

                        // Si confirmamos un bloque, disparar el filtro GIS
                        if (config.selectId === 'selectBloqueEdit') {
                            select.dispatchEvent(new Event('change'));
                        }
                        
                        var originalPlaceholder = this.placeholder;
                        this.placeholder = '✅ ' + selectedText.substring(0, 35);
                        this.style.background = '#d4edda';
                        var self = this;
                        setTimeout(function() {
                            self.placeholder = originalPlaceholder;
                            self.style.background = '';
                        }, 1500);
                    }
                    return false;
                }
                
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    if (select.selectedIndex < select.options.length - 1) {
                        select.selectedIndex++;
                        updateLabel();
                    }
                }
                if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    if (select.selectedIndex > 0) {
                        select.selectedIndex--;
                        updateLabel();
                    }
                }
            });
            
            select.addEventListener('change', updateLabel);
            select.addEventListener('click', updateLabel);

            // Inicializar label con valor actual del nicho
            updateLabel();
        });
    })();

    // ========== INICIALIZAR VALORES ACTUALES DEL NICHO ==========
    (function() {
        function selectAndLabel(selectId, labelId, targetValue) {
            var sel = document.getElementById(selectId);
            var lbl = document.getElementById(labelId);
            if (!sel || !lbl || !targetValue) return;

            for (var i = 0; i < sel.options.length; i++) {
                if (sel.options[i].value == targetValue) {
                    sel.selectedIndex = i;
                    sel.options[i].selected = true;
                    lbl.textContent = sel.options[i].text;
                    lbl.className = 'fw-bold text-success';
                    // Scroll para que se vea el item seleccionado
                    sel.scrollTop = sel.options[i].offsetTop - sel.offsetTop;
                    break;
                }
            }
        }

        // Preseleccionar Bloque (valor del servidor)
        selectAndLabel('selectBloqueEdit', 'bloqueSeleccionadoEdit', '{{ old("bloque_id", $nicho->bloque_id) }}');

        // Preseleccionar Socio (valor del servidor)
        selectAndLabel('selectSocioEdit', 'socioSeleccionadoEdit', '{{ old("socio_id", $nicho->socio_id) }}');

        // GIS se preselecciona desde el callback del API (ya implementado en el cascade)
    })();
</script>