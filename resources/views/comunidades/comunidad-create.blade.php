<style>
    .search-match { background: linear-gradient(90deg, #d4edda 0%, #c3e6cb 100%) !important; font-weight: 600 !important; }
    .search-first-match { background: linear-gradient(90deg, #28a745 0%, #20c997 100%) !important; color: white !important; font-weight: 700 !important; }
    .search-input-found { border: 2px solid #28a745 !important; box-shadow: 0 0 8px rgba(40, 167, 69, 0.4) !important; }
    .search-input-empty { border: 2px solid #dc3545 !important; box-shadow: 0 0 8px rgba(220, 53, 69, 0.4) !important; }
</style>

{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nueva Comunidad</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('comunidades.store') }}">
    @csrf
    
    <div class="modal-body">
        
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código se genera automáticamente.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Cantón (Buscable) --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Cantón <span class="text-danger">*</span></label>
                <input type="text" id="buscarCanton" class="form-control mb-2" placeholder="🔍 Buscar cantón...">
                <select id="canton_select" class="form-select" required size="3" style="height: auto;">
                    <option value="">-- Seleccionar Cantón --</option>
                    @foreach(\App\Models\Canton::orderBy('nombre')->get(['id','nombre']) as $c)
                        <option value="{{ $c->id }}" 
                            data-search="{{ strtolower($c->nombre) }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Seleccionado: <span id="cantonSeleccionado" class="fw-bold text-primary">Ninguno</span></small>
            </div>

            {{-- Parroquia (Buscable, se carga dinámicamente) --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Parroquia <span class="text-danger">*</span></label>
                <input type="text" id="buscarParroquia" class="form-control mb-2" placeholder="🔍 Buscar parroquia..." disabled>
                <select name="parroquia_id" id="parroquia_select" class="form-select" required size="3" style="height: auto;" disabled>
                    <option value="">Seleccione un cantón primero...</option>
                </select>
                <small class="text-muted">Seleccionado: <span id="parroquiaSeleccionado" class="fw-bold text-primary">Ninguno</span></small>
            </div>

            {{-- Nombre --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre Comunidad <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre') }}" class="form-control" required maxlength="255" placeholder="Ej: San Francisco">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>

<script>
(function() {
    // ========== BÚSQUEDA PARA CANTÓN ==========
    const buscarCanton = document.getElementById('buscarCanton');
    const selectCanton = document.getElementById('canton_select');
    const labelCanton = document.getElementById('cantonSeleccionado');

    const buscarParroquia = document.getElementById('buscarParroquia');
    const selectParroquia = document.getElementById('parroquia_select');
    const labelParroquia = document.getElementById('parroquiaSeleccionado');

    // Guardar opciones originales del cantón
    const cantonOptions = [];
    Array.from(selectCanton.options).forEach(opt => {
        cantonOptions.push({
            value: opt.value,
            text: opt.text,
            searchData: (opt.getAttribute('data-search') || opt.text).toLowerCase()
        });
    });

    // Variable para opciones dinámicas de parroquia
    let parroquiaOptions = [];

    // ---- Funciones genéricas de búsqueda ----
    function filterSelect(input, select, label, options, searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        select.innerHTML = '';
        let matchCount = 0;
        let firstMatchIndex = -1;

        options.forEach(optData => {
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
                    updateLabel(select, label);
                }
            } else {
                input.classList.add('search-input-empty');
            }
        }
    }

    function updateLabel(select, label) {
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

    function setupSearch(input, select, label, getOptions) {
        input.addEventListener('input', function() {
            filterSelect(input, select, label, getOptions(), this.value);
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                const selectedOpt = select.options[select.selectedIndex];
                if (selectedOpt && selectedOpt.value !== '') {
                    const val = selectedOpt.value;
                    const txt = selectedOpt.text;
                    this.value = '';
                    this.classList.remove('search-input-found', 'search-input-empty');
                    filterSelect(input, select, label, getOptions(), '');
                    for (let i = 0; i < select.options.length; i++) {
                        if (select.options[i].value === val) { select.selectedIndex = i; break; }
                    }
                    updateLabel(select, label);
                    this.placeholder = '✅ ' + txt.substring(0, 35);
                    this.style.background = '#d4edda';
                    setTimeout(() => { this.placeholder = this.dataset.ph || '🔍 Buscar...'; this.style.background = ''; }, 1500);
                }
                return false;
            }
            if (e.key === 'ArrowDown') { e.preventDefault(); if (select.selectedIndex < select.options.length - 1) { select.selectedIndex++; updateLabel(select, label); } }
            if (e.key === 'ArrowUp') { e.preventDefault(); if (select.selectedIndex > 0) { select.selectedIndex--; updateLabel(select, label); } }
        });

        select.addEventListener('change', () => updateLabel(select, label));
        select.addEventListener('click', () => updateLabel(select, label));
    }

    // Guardar placeholders originales
    buscarCanton.dataset.ph = buscarCanton.placeholder;
    buscarParroquia.dataset.ph = buscarParroquia.placeholder;

    // Inicializar búsqueda de Cantón
    setupSearch(buscarCanton, selectCanton, labelCanton, () => cantonOptions);

    // Inicializar búsqueda de Parroquia
    setupSearch(buscarParroquia, selectParroquia, labelParroquia, () => parroquiaOptions);

    // ========== CARGA DINÁMICA: Cantón → Parroquia ==========
    selectCanton.addEventListener('change', async function() {
        const cantonId = this.value;

        // Resetear parroquia
        buscarParroquia.value = '';
        buscarParroquia.classList.remove('search-input-found', 'search-input-empty');
        parroquiaOptions = [];
        selectParroquia.innerHTML = '<option value="">Cargando...</option>';
        labelParroquia.textContent = 'Ninguno';
        labelParroquia.classList.remove('text-success');
        labelParroquia.classList.add('text-primary');

        if (!cantonId) {
            selectParroquia.innerHTML = '<option value="">Seleccione un cantón primero...</option>';
            selectParroquia.disabled = true;
            buscarParroquia.disabled = true;
            return;
        }

        try {
            const response = await fetch("{{ url('cantones') }}/" + cantonId + "/parroquias");
            if (!response.ok) throw new Error('Error en la red');
            const data = await response.json();

            selectParroquia.disabled = false;
            buscarParroquia.disabled = false;

            // Reconstruir opciones
            parroquiaOptions = [{ value: '', text: '-- Seleccionar --', searchData: '' }];
            data.forEach(p => {
                parroquiaOptions.push({ value: String(p.id), text: p.nombre, searchData: p.nombre.toLowerCase() });
            });

            if (data.length === 0) {
                parroquiaOptions = [{ value: '', text: 'No hay parroquias', searchData: '' }];
                buscarParroquia.disabled = true;
            }

            // Renderizar
            filterSelect(buscarParroquia, selectParroquia, labelParroquia, parroquiaOptions, '');

        } catch (err) {
            console.error(err);
            selectParroquia.innerHTML = '<option value="">Error al cargar</option>';
        }
    });
})();
</script>