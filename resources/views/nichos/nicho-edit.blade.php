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

    // ========== BÚSQUEDA AJAX PARA BLOQUES Y SOCIOS (Edit) ==========
    (function() {
        var currentBloqueId = '{{ old("bloque_id", $nicho->bloque_id) }}';
        var currentSocioId = '{{ old("socio_id", $nicho->socio_id) }}';

        // ── Búsqueda de Bloques (AJAX) ──
        (function() {
            var input = document.getElementById('buscarBloqueEdit');
            var select = document.getElementById('selectBloqueEdit');
            var label = document.getElementById('bloqueSeleccionadoEdit');
            if (!input || !select) return;

            var debounceTimer = null;

            function renderBloques(data, keepVal) {
                select.innerHTML = '<option value="">-- Seleccionar --</option>';
                data.forEach(function(b) {
                    var opt = document.createElement('option');
                    opt.value = b.identificacion;
                    opt.textContent = b.nombre;
                    if (String(b.identificacion) === String(keepVal)) opt.selected = true;
                    select.appendChild(opt);
                });
                updateLabel();
            }

            function fetchBloques(q) {
                fetch('/api/bloques/search?q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        renderBloques(data, currentBloqueId);
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

            function updateLabel() {
                if (!label) return;
                var opt = select.options[select.selectedIndex];
                if (opt && opt.value !== '') { label.textContent = opt.text; label.className = 'fw-bold text-success'; }
                else { label.textContent = 'Ninguno'; label.className = 'fw-bold text-primary'; }
            }

            fetchBloques('');

            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                var val = input.value;
                debounceTimer = setTimeout(function() { fetchBloques(val); }, 300);
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); e.stopPropagation();
                    var opt = select.options[select.selectedIndex];
                    if (opt && opt.value !== '') {
                        currentBloqueId = opt.value;
                        var txt = opt.text;
                        input.value = '';
                        input.classList.remove('search-input-found', 'search-input-empty');
                        fetchBloques('');
                        setTimeout(function() {
                            for (var i = 0; i < select.options.length; i++) {
                                if (select.options[i].value === currentBloqueId) { select.selectedIndex = i; break; }
                            }
                            updateLabel();
                            select.dispatchEvent(new Event('change'));
                        }, 400);
                        input.placeholder = '✅ ' + txt.substring(0, 35);
                        input.style.background = '#d4edda';
                        setTimeout(function() { input.placeholder = '🔍 Buscar bloque...'; input.style.background = ''; }, 1500);
                    }
                    return false;
                }
                if (e.key === 'ArrowDown') { e.preventDefault(); if (select.selectedIndex < select.options.length - 1) { select.selectedIndex++; updateLabel(); } }
                if (e.key === 'ArrowUp') { e.preventDefault(); if (select.selectedIndex > 0) { select.selectedIndex--; updateLabel(); } }
            });

            select.addEventListener('change', updateLabel);
            select.addEventListener('click', updateLabel);
        })();

        // ── Búsqueda de Socios (AJAX) ──
        (function() {
            var input = document.getElementById('buscarSocioEdit');
            var select = document.getElementById('selectSocioEdit');
            var label = document.getElementById('socioSeleccionadoEdit');
            if (!input || !select) return;

            var debounceTimer = null;

            function renderSocios(data, keepVal) {
                select.innerHTML = '<option value="">-- Seleccionar --</option>';
                data.forEach(function(s) {
                    var opt = document.createElement('option');
                    opt.value = s.id;
                    opt.textContent = s.apellidos + ' ' + s.nombres + ' (' + s.cedula + ')';
                    if (String(s.id) === String(keepVal)) opt.selected = true;
                    select.appendChild(opt);
                });
                updateLabel();
            }

            function fetchSocios(q) {
                fetch('/api/socios/search?q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        renderSocios(data, currentSocioId);
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

            function updateLabel() {
                if (!label) return;
                var opt = select.options[select.selectedIndex];
                if (opt && opt.value !== '') { label.textContent = opt.text; label.className = 'fw-bold text-success'; }
                else { label.textContent = 'Ninguno'; label.className = 'fw-bold text-primary'; }
            }

            fetchSocios('');

            input.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                var val = input.value;
                debounceTimer = setTimeout(function() { fetchSocios(val); }, 300);
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault(); e.stopPropagation();
                    var opt = select.options[select.selectedIndex];
                    if (opt && opt.value !== '') {
                        currentSocioId = opt.value;
                        var txt = opt.text;
                        input.value = '';
                        input.classList.remove('search-input-found', 'search-input-empty');
                        fetchSocios('');
                        setTimeout(function() {
                            for (var i = 0; i < select.options.length; i++) {
                                if (select.options[i].value === currentSocioId) { select.selectedIndex = i; break; }
                            }
                            updateLabel();
                        }, 400);
                        input.placeholder = '✅ ' + txt.substring(0, 35);
                        input.style.background = '#d4edda';
                        setTimeout(function() { input.placeholder = '🔍 Buscar por nombre o cédula...'; input.style.background = ''; }, 1500);
                    }
                    return false;
                }
                if (e.key === 'ArrowDown') { e.preventDefault(); if (select.selectedIndex < select.options.length - 1) { select.selectedIndex++; updateLabel(); } }
                if (e.key === 'ArrowUp') { e.preventDefault(); if (select.selectedIndex > 0) { select.selectedIndex--; updateLabel(); } }
            });

            select.addEventListener('change', updateLabel);
            select.addEventListener('click', updateLabel);
        })();

        // ── Búsqueda de GIS (Client-side, se carga por API al seleccionar bloque) ──
        (function() {
            var input = document.getElementById('buscarGisEdit');
            var select = document.getElementById('selectGisEdit');
            var label = document.getElementById('gisSeleccionadoEdit');
            if (!input || !select) return;

            var allGisOptions = [];
            function captureGis() {
                allGisOptions = [];
                Array.from(select.options).forEach(function(opt) {
                    allGisOptions.push({ value: opt.value, text: opt.text, disabled: opt.disabled, search: (opt.getAttribute('data-search') || opt.text).toLowerCase() });
                });
            }
            captureGis();
            var obs = new MutationObserver(captureGis);
            obs.observe(select, { childList: true });

            function updateLabel() {
                if (!label) return;
                var opt = select.options[select.selectedIndex];
                if (opt && opt.value !== '') { label.textContent = opt.text; label.className = 'fw-bold text-success'; }
                else { label.textContent = 'Ninguno'; label.className = 'fw-bold text-primary'; }
            }

            input.addEventListener('input', function() {
                var term = this.value.toLowerCase().trim();
                select.innerHTML = '';
                var count = 0, first = -1;
                allGisOptions.forEach(function(o) {
                    if (term === '' || o.value === '' || o.search.includes(term)) {
                        var opt = document.createElement('option');
                        opt.value = o.value; opt.textContent = o.text;
                        if (o.disabled) opt.disabled = true;
                        if (term !== '' && o.value !== '' && !o.disabled && o.search.includes(term)) {
                            count++;
                            if (first === -1) { first = select.options.length; opt.className = 'search-first-match'; }
                            else { opt.className = 'search-match'; }
                        }
                        select.appendChild(opt);
                    }
                });
                setTimeout(function() { captureGis(); }, 50);
                input.classList.remove('search-input-found', 'search-input-empty');
                if (term !== '') {
                    if (count > 0) { input.classList.add('search-input-found'); if (first !== -1) { select.selectedIndex = first; updateLabel(); } }
                    else { input.classList.add('search-input-empty'); }
                }
            });

            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') { e.preventDefault(); updateLabel(); return false; }
                if (e.key === 'ArrowDown') { e.preventDefault(); if (select.selectedIndex < select.options.length - 1) { select.selectedIndex++; updateLabel(); } }
                if (e.key === 'ArrowUp') { e.preventDefault(); if (select.selectedIndex > 0) { select.selectedIndex--; updateLabel(); } }
            });

            select.addEventListener('change', updateLabel);
            select.addEventListener('click', updateLabel);
            updateLabel();
        })();
    })();
</script>