<style>
    /* Estilos de búsqueda (idénticos a asignación) */
    .search-match { background: linear-gradient(90deg, #d4edda 0%, #c3e6cb 100%) !important; font-weight: 600 !important; }
    .search-first-match { background: linear-gradient(90deg, #28a745 0%, #20c997 100%) !important; color: white !important; font-weight: 700 !important; }
    .search-input-found { border: 2px solid #28a745 !important; box-shadow: 0 0 8px rgba(40, 167, 69, 0.4) !important; }
    .search-input-empty { border: 2px solid #dc3545 !important; box-shadow: 0 0 8px rgba(220, 53, 69, 0.4) !important; }
</style>

<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">
        <i class="fa-solid fa-monument me-2"></i> Nuevo Nicho
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<form method="POST" action="{{ route('nichos.store') }}">
    @csrf
    <div class="modal-body">
        
        <div class="alert alert-info py-2 mb-3 text-xs shadow-sm border-0" style="background-color: #e7f1ff; color: #0c5460;">
            <i class="fas fa-info-circle me-1"></i> El <b>Código</b> se genera automáticamente (o selecciona del mapa).
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
                <button class="nav-link active fw-bold small" data-bs-toggle="tab" data-bs-target="#ubicacion" type="button">
                    <i class="fas fa-map-marker-alt me-1"></i> Ubicación
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#caracteristicas" type="button">
                    <i class="fas fa-cogs me-1"></i> Datos Técnicos
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            {{-- TAB 1: UBICACIÓN --}}
            <div class="tab-pane fade show active" id="ubicacion">
                <div class="row g-3">

                    {{-- GIS (Buscable) --}}
                    <div class="col-12">
                        <label class="form-label fw-bold text-primary small">Mapa GIS (Opcional)</label>
                        <input type="text" id="buscarGis" class="form-control mb-2" placeholder="🔍 Buscar código en mapa...">
                        <select name="nicho_geom_id" id="selectGis" class="form-select" size="2" style="height: auto;">
                            <option value="">-- Ninguno (Manual) --</option>
                            @isset($nichosGeom)
                                @foreach($nichosGeom as $ng)
                                    <option value="{{ $ng->id }}" 
                                        data-search="{{ strtolower($ng->codigo) }}"
                                        @selected(old('nicho_geom_id') == $ng->id)>{{ $ng->codigo }}</option>
                                @endforeach
                            @endisset
                        </select>
                        <small class="text-muted">Seleccionado: <span id="gisSeleccionado" class="fw-bold text-primary">Ninguno</span></small>
                    </div>

                    {{-- Bloque (Buscable) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Bloque <span class="text-danger">*</span></label>
                        <input type="text" id="buscarBloque" class="form-control mb-2" placeholder="🔍 Buscar bloque...">
                        <select name="bloque_id" id="selectBloque" class="form-select" required size="2" style="height: auto;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($bloques as $b)
                                <option value="{{ $b->id }}" 
                                    data-search="{{ strtolower($b->nombre . ' ' . ($b->codigo ?? '')) }}"
                                    @selected(old('bloque_id') == $b->id)>{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Seleccionado: <span id="bloqueSeleccionado" class="fw-bold text-primary">Ninguno</span></small>
                    </div>
                    
                    {{-- Socio (Buscable) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Socio Titular <span class="text-danger">*</span></label>
                        <input type="text" id="buscarSocio" class="form-control mb-2" placeholder="🔍 Buscar por nombre o cédula...">
                        <select name="socio_id" id="selectSocio" class="form-select" required size="2" style="height: auto;">
                            <option value="">-- Seleccionar --</option>
                            @foreach($socios as $s)
                                <option value="{{ $s->id }}" 
                                    data-search="{{ strtolower($s->cedula . ' ' . $s->apellidos . ' ' . $s->nombres) }}"
                                    @selected(old('socio_id') == $s->id)>{{ $s->apellidos }} {{ $s->nombres }} ({{ $s->cedula }})</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Seleccionado: <span id="socioSeleccionado" class="fw-bold text-primary">Ninguno</span></small>
                    </div>
                </div>
            </div>

            {{-- TAB 2: DATOS TÉCNICOS --}}
            <div class="tab-pane fade" id="caracteristicas">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Tipo</label>
                        <select name="tipo_nicho" class="form-select" required>
                            <option value="PROPIO" @selected(old('tipo_nicho') == 'PROPIO')>PROPIO</option>
                            <option value="COMPARTIDO" @selected(old('tipo_nicho') == 'COMPARTIDO')>COMPARTIDO</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Clase</label>
                        <select name="clase_nicho" class="form-select" required>
                            <option value="BOVEDA" @selected(old('clase_nicho') == 'BOVEDA')>Bóveda</option>
                            <option value="TIERRA" @selected(old('clase_nicho') == 'TIERRA')>Tierra</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Capacidad</label>
                        <input type="number" name="capacidad" min="1" value="{{ old('capacidad', 3) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Estado</label>
                        <select name="estado" class="form-select" required>
                            <option value="BUENO">Bueno</option>
                            <option value="MANTENIMIENTO">Mantenimiento</option>
                            <option value="MALO">Malo</option>
                            <option value="ABANDONADO">Abandonado</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Notas</label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success fw-bold px-4">Guardar</button>
    </div>
</form>

<script>
    // ========== BÚSQUEDA PARA SELECTS (Mismo patrón que asignaciones) ==========
    (function() {
        const configs = [
            { inputId: 'buscarGis', selectId: 'selectGis', labelId: 'gisSeleccionado' },
            { inputId: 'buscarBloque', selectId: 'selectBloque', labelId: 'bloqueSeleccionado' },
            { inputId: 'buscarSocio', selectId: 'selectSocio', labelId: 'socioSeleccionado' }
        ];

        configs.forEach(config => {
            const input = document.getElementById(config.inputId);
            const select = document.getElementById(config.selectId);
            const label = document.getElementById(config.labelId);
            
            if (!input || !select) return;
            
            // Guardar opciones originales
            const allOptions = [];
            Array.from(select.options).forEach(opt => {
                allOptions.push({
                    value: opt.value,
                    text: opt.text,
                    selected: opt.selected,
                    searchData: (opt.getAttribute('data-search') || opt.text).toLowerCase()
                });
            });
            
            function updateOptions(searchTerm) {
                const term = searchTerm.toLowerCase().trim();
                select.innerHTML = '';
                let matchCount = 0;
                let firstMatchIndex = -1;
                
                allOptions.forEach((optData) => {
                    const matches = optData.value === '' || 
                                   optData.searchData.includes(term) || 
                                   optData.text.toLowerCase().includes(term);
                    
                    if (term === '' || matches) {
                        const option = document.createElement('option');
                        option.value = optData.value;
                        option.textContent = optData.text;
                        
                        if (term !== '' && optData.value !== '' && matches) {
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
                const selectedOpt = select.options[select.selectedIndex];
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
                    
                    const selectedOpt = select.options[select.selectedIndex];
                    if (selectedOpt && selectedOpt.value !== '') {
                        const selectedValue = selectedOpt.value;
                        const selectedText = selectedOpt.text;
                        
                        this.value = '';
                        this.classList.remove('search-input-found', 'search-input-empty');
                        updateOptions('');
                        
                        for (let i = 0; i < select.options.length; i++) {
                            if (select.options[i].value === selectedValue) {
                                select.selectedIndex = i;
                                break;
                            }
                        }
                        updateLabel();
                        
                        const originalPlaceholder = this.placeholder;
                        this.placeholder = '✅ ' + selectedText.substring(0, 35);
                        this.style.background = '#d4edda';
                        setTimeout(() => {
                            this.placeholder = originalPlaceholder;
                            this.style.background = '';
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

            // Inicializar label si hay old() seleccionado
            updateLabel();
        });
    })();
</script>