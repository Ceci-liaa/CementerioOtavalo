<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        /* ALERTAS */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; font-size: 14px !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }
        
        .alert-danger { background-color: #fde1e1 !important; color: #cf304a !important; border-color: #fde1e1 !important; font-size: 14px !important; }
        .alert-danger .btn-close { filter: none !important; opacity: 0.5; color: #cf304a; }

        /* BUSCADOR Y FILTROS */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 200px; }

        /* ESTILOS DE TABLA */
        .table thead th { font-size: 14px !important; text-transform: uppercase; letter-spacing: 0.05rem; font-weight: 700 !important; padding-top: 15px !important; padding-bottom: 15px !important; }
        .btn-action { margin-right: 4px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">

            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Fallecidos</h3>
                        <span class="badge bg-light text-dark border" id="fallecidosTotalBadge" style="font-size: 0.8rem;">
                            Total: {{ $fallecidos->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Gestión y reportes de registros de defunción.</p>
                </div>

                {{-- Botón Nuevo --}}
                <button type="button" class="btn btn-success px-4 open-modal" style="height: fit-content;"
                    data-url="{{ route('fallecidos.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Registro
                </button>
            </div>

            {{-- 3. ALERTAS --}}
            <div class="mb-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show alert-temporal" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>No se pudo guardar:</strong>
                                <ul class="mb-0 ps-3" style="list-style-type: none; padding-left: 0;">
                                    @foreach ($errors->all() as $error) <li>- {{ $error }}</li> @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            {{-- INICIO DEL FORMULARIO DE REPORTE (Abarca Filtros y Tabla) --}}
            <form action="{{ route('fallecidos.reports') }}" method="POST" id="reportForm">
                @csrf
                
                {{-- Inputs ocultos para filtros en reporte --}}
                <input type="hidden" name="current_search" value="{{ request('search') }}">
                <input type="hidden" name="current_comunidad" value="{{ request('comunidad_id') }}">
                <input type="hidden" name="current_mes" value="{{ request('mes') }}">
                <input type="hidden" name="current_anio" value="{{ request('anio') }}">

                {{-- 4. BARRA DE HERRAMIENTAS --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">

                    {{-- Lado Izquierdo: Reportes --}}
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto"
                            style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" type="button"
                            data-bs-toggle="dropdown">
                            Generar Reporte
                        </button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i
                                        class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i
                                        class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    {{-- Lado Derecho: Filtros --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        {{-- Filtro Año --}}
                        <select id="anioFilter" class="form-select form-select-sm compact-filter ps-2" style="max-width: 100px;">
                            <option value="">Año</option>
                            @for($i = date('Y'); $i >= 1900; $i--)
                                <option value="{{ $i }}" @selected(request('anio') == $i)>{{ $i }}</option>
                            @endfor
                        </select>

                        {{-- Filtro Mes --}}
                        <select id="mesFilter" class="form-select form-select-sm compact-filter ps-2" style="max-width: 110px;">
                            <option value="">Mes</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" @selected(request('mes') == $m)>
                                    {{ ucfirst(\Carbon\Carbon::create(null, $m, 1)->locale('es')->monthName) }}
                                </option>
                            @endforeach
                        </select>

                        <select id="comunidadFilter" class="form-select form-select-sm compact-filter ps-2">
                            <option value="">Toda Comunidad</option>
                            @foreach($comunidades as $c)
                                <option value="{{ $c->id }}" @selected(request('comunidad_id') == $c->id)>{{ $c->nombre }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Buscador con Spinner --}}
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary">
                                <i class="fas fa-search" id="searchIcon"></i>
                                <i class="fas fa-spinner fa-spin text-primary" id="searchSpinner" style="display:none;"></i>
                            </span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" placeholder="Buscar..."
                                id="searchInput" value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                {{-- 5. TABLA --}}
                <div class="card shadow-sm border">
                    <div class="card-body p-0 pb-2">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th class="opacity-10" style="width: 40px;">
                                            <input type="checkbox" id="selectAll" style="cursor: pointer;">
                                        </th>
                                        <th class="opacity-10" style="width: 50px;">#</th>
                                        <th class="opacity-10">Código</th>
                                        <th class="opacity-10">Cédula</th>
                                        <th class="opacity-10 text-start ps-4">Apellidos y Nombres</th>
                                        <th class="opacity-10">Comunidad</th>
                                        <th class="opacity-10">Fecha Fall.</th>
                                        <th class="opacity-10" style="width:180px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="fallecidosTbody">
                                    @forelse ($fallecidos as $f)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ids[]" value="{{ $f->id }}" class="check-item"
                                                    style="cursor: pointer;">
                                            </td>

                                            <td class="text-sm fw-bold text-secondary">
                                                {{ $fallecidos->firstItem() + $loop->index }}
                                            </td>
                                            <td class="fw-bold text-dark">{{ $f->codigo }}</td>
                                            <td class="text-secondary text-sm">{{ $f->cedula ?? 'S/N' }}</td>
                                            <td class="text-start ps-4">
                                                <span class="text-sm font-weight-bold">{{ $f->apellidos }} {{ $f->nombres }}</span>
                                            </td>
                                            <td>
                                                <span class="badge border"
                                                    style="background-color: #f8f9fa; color: #343a40; font-size: 0.75rem; font-weight: 600;">
                                                    {{ $f->comunidad->nombre ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="text-secondary text-sm">
                                                {{ $f->fecha_fallecimiento ? $f->fecha_fallecimiento->format('d/m/Y') : '-' }}
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    {{-- Botón Ver --}}
                                                    <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal"
                                                        data-url="{{ route('fallecidos.show', $f->id) }}" title="Ver">
                                                        <i class="fa-solid fa-eye text-white" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    {{-- Botón Editar --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-warning mb-0 btn-action open-modal"
                                                        data-url="{{ route('fallecidos.edit', $f->id) }}" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    {{-- Botón Eliminar --}}
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger mb-0 btn-action js-delete-btn"
                                                        data-url="{{ route('fallecidos.destroy', $f) }}"
                                                        data-item="{{ $f->apellidos }} {{ $f->nombres }}" title="Eliminar">
                                                        <i class="fa-solid fa-trash" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5 text-muted">
                                                No se encontraron registros de fallecidos.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        <div class="mt-3 px-3 d-flex justify-content-end" id="fallecidosPagination">
                            @if($fallecidos->hasPages())
                                {{ $fallecidos->appends(request()->query())->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </form> {{-- FIN DEL FORMULARIO --}}

            {{-- Formulario de Eliminación (Fuera del reporte) --}}
            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content"></div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS Y LIBRERÍAS --}}
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // 1. Alertas temporales
                setTimeout(() => {
                    document.querySelectorAll('.alert-temporal').forEach(alert => {
                        alert.style.transition = "opacity 0.5s"; alert.style.opacity = 0;
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 3000);

                // 2. Referencias
                const searchInput = document.getElementById('searchInput');
                const comunidadFilter = document.getElementById('comunidadFilter');
                const mesFilter = document.getElementById('mesFilter');
                const anioFilter = document.getElementById('anioFilter');

                // ── BÚSQUEDA EN TIEMPO REAL (AJAX) ──────────────────────────────
                let searchTimeout = null;
                let currentAbort = null;
                let requestId = 0;

                function buildFilterParams() {
                    const params = new URLSearchParams();
                    if (searchInput && searchInput.value.trim()) params.set('search', searchInput.value.trim());
                    if (comunidadFilter && comunidadFilter.value) params.set('comunidad_id', comunidadFilter.value);
                    if (anioFilter && anioFilter.value) params.set('anio', anioFilter.value);
                    if (mesFilter && mesFilter.value) params.set('mes', mesFilter.value);
                    return params;
                }

                function showSpinner() {
                    const ic = document.getElementById('searchIcon');
                    const sp = document.getElementById('searchSpinner');
                    if (ic) ic.style.display = 'none';
                    if (sp) sp.style.display = 'inline-block';
                }

                function hideSpinner() {
                    const ic = document.getElementById('searchIcon');
                    const sp = document.getElementById('searchSpinner');
                    if (ic) ic.style.display = 'inline-block';
                    if (sp) sp.style.display = 'none';
                }

                function liveSearch() {
                    // Cancelar petición anterior
                    if (currentAbort) currentAbort.abort();
                    currentAbort = new AbortController();

                    const myId = ++requestId;
                    showSpinner();

                    const params = buildFilterParams();
                    const url = "{{ route('fallecidos.index') }}?" + params.toString();
                    window.history.replaceState({}, '', url);

                    // Actualizar inputs ocultos del reporte
                    const hiddenSearch = document.querySelector('input[name="current_search"]');
                    const hiddenComunidad = document.querySelector('input[name="current_comunidad"]');
                    const hiddenMes = document.querySelector('input[name="current_mes"]');
                    const hiddenAnio = document.querySelector('input[name="current_anio"]');
                    if (hiddenSearch) hiddenSearch.value = searchInput ? searchInput.value.trim() : '';
                    if (hiddenComunidad) hiddenComunidad.value = comunidadFilter ? comunidadFilter.value : '';
                    if (hiddenMes) hiddenMes.value = mesFilter ? mesFilter.value : '';
                    if (hiddenAnio) hiddenAnio.value = anioFilter ? anioFilter.value : '';

                    fetch(url, { signal: currentAbort.signal })
                        .then(function(r) { return r.text(); })
                        .then(function(html) {
                            // Solo procesar si es la petición más reciente
                            if (myId !== requestId) return;

                            var doc = new DOMParser().parseFromString(html, 'text/html');

                            // Reemplazar tbody
                            var newTbody = doc.getElementById('fallecidosTbody');
                            var curTbody = document.getElementById('fallecidosTbody');
                            if (newTbody && curTbody) curTbody.innerHTML = newTbody.innerHTML;

                            // Reemplazar paginación
                            var newPag = doc.getElementById('fallecidosPagination');
                            var curPag = document.getElementById('fallecidosPagination');
                            if (newPag && curPag) curPag.innerHTML = newPag.innerHTML;

                            // Reemplazar total
                            var newBadge = doc.getElementById('fallecidosTotalBadge');
                            var curBadge = document.getElementById('fallecidosTotalBadge');
                            if (newBadge && curBadge) curBadge.textContent = newBadge.textContent;

                            hideSpinner();
                        })
                        .catch(function(e) {
                            if (e.name !== 'AbortError') {
                                console.error('Error en búsqueda:', e);
                                hideSpinner();
                            }
                        });
                }

                function triggerSearch() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(liveSearch, 350);
                }

                // Input de búsqueda: tiempo real
                if (searchInput) {
                    searchInput.addEventListener('input', triggerSearch);
                    searchInput.addEventListener('keyup', triggerSearch);
                    searchInput.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') e.preventDefault();
                    });
                }

                // Los demás filtros también disparan liveSearch
                if (comunidadFilter) comunidadFilter.addEventListener('change', liveSearch);
                if (mesFilter) mesFilter.addEventListener('change', liveSearch);
                if (anioFilter) anioFilter.addEventListener('change', liveSearch);

                // 3. Modal AJAX (Delegación de eventos para funcionar con filas AJAX)
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                
                document.body.addEventListener('click', function (e) {
                    const btn = e.target.closest('.open-modal');
                    if (!btn) return;
                    e.preventDefault();

                    modalEl.querySelector('.modal-content').innerHTML = `
                        <div class="p-5 text-center">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2 text-secondary">Cargando...</p>
                        </div>`;
                    modal.show();
                    
                    fetch(btn.getAttribute('data-url'))
                        .then(r => r.text())
                        .then(h => { 
                            modalEl.querySelector('.modal-content').innerHTML = h;
                            // Ejecutar scripts que vienen dentro del HTML cargado
                            modalEl.querySelectorAll('.modal-content script').forEach(oldScript => {
                                const newScript = document.createElement('script');
                                if (oldScript.src) {
                                    newScript.src = oldScript.src;
                                } else {
                                    newScript.textContent = oldScript.textContent;
                                }
                                oldScript.parentNode.replaceChild(newScript, oldScript);
                            });
                        });
                });

                // 4. Delete SweetAlert (Delegación de eventos para funcionar con filas AJAX)
                document.body.addEventListener('click', function (e) {
                    const btn = e.target.closest('.js-delete-btn');
                    if (!btn) return;
                    e.preventDefault();
                    
                    Swal.fire({
                        title: '¿Eliminar Registro?',
                        html: `¿Deseas eliminar a <b>"${btn.getAttribute('data-item')}"</b>?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((r) => {
                        if (r.isConfirmed) {
                            const f = document.getElementById('deleteForm');
                            f.action = btn.getAttribute('data-url');
                            f.submit();
                        }
                    });
                });

                // 5. Select All (Delegación de eventos)
                const selectAll = document.getElementById('selectAll');
                if(selectAll) {
                    selectAll.addEventListener('change', function() {
                        const checked = this.checked;
                        document.querySelectorAll('.check-item').forEach(checkbox => {
                            checkbox.checked = checked;
                        });
                    });
                }

                // 6. VALIDACIÓN POR PESTAÑAS EN MODALES
                modalEl.addEventListener('submit', function(e) {
                    const form = e.target;
                    if (!form.matches('form')) return;

                    const campos = form.querySelectorAll('input[required], select[required]');
                    let vaciosPersonal = [];
                    let vaciosDetalles = [];

                    campos.forEach(function(campo) {
                        if (!campo.value || campo.value.trim() === '') {
                            if (campo.closest('#personal') || campo.closest('#edit-personal')) {
                                vaciosPersonal.push(campo);
                            }
                            if (campo.closest('#detalles') || campo.closest('#edit-detalles')) {
                                vaciosDetalles.push(campo);
                            }
                        }
                    });

                    if (vaciosPersonal.length === 0 && vaciosDetalles.length === 0) return;

                    e.preventDefault();

                    const prev = form.querySelector('.alerta-validacion');
                    if (prev) prev.remove();

                    const tabPersonalActivo = modalEl.querySelector('#personal.show.active, #edit-personal.show.active');
                    const tabDetallesActivo = modalEl.querySelector('#detalles.show.active, #edit-detalles.show.active');

                    let msg = '';
                    let irAPestana = null;

                    if (vaciosPersonal.length > 0 && vaciosDetalles.length > 0) {
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Faltan campos obligatorios en <strong>Personal</strong> y en <strong>Detalles y Notas</strong>. Por favor complételos.';
                    } else if (vaciosDetalles.length > 0 && tabPersonalActivo) {
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Vaya al apartado <strong>Detalles y Notas</strong> y complete los campos obligatorios.';
                        irAPestana = '[data-bs-target="#detalles"], [data-bs-target="#edit-detalles"]';
                    } else if (vaciosPersonal.length > 0 && tabDetallesActivo) {
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Vaya al apartado <strong>Personal</strong> y complete los campos obligatorios.';
                        irAPestana = '[data-bs-target="#personal"], [data-bs-target="#edit-personal"]';
                    } else if (vaciosPersonal.length > 0) {
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Complete los campos obligatorios en esta pestaña.';
                    } else {
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Complete los campos obligatorios en esta pestaña.';
                    }

                    const alerta = document.createElement('div');
                    alerta.className = 'alert alert-warning py-2 mb-0 mt-2 alerta-validacion d-flex align-items-center';
                    alerta.style.cssText = 'font-size: 0.85rem; border-left: 4px solid #ffc107;';
                    alerta.innerHTML = msg;

                    const body = form.querySelector('.modal-body');
                    if (body) body.appendChild(alerta);

                    if (irAPestana) {
                        setTimeout(function() {
                            const btn = modalEl.querySelector(irAPestana);
                            if (btn) btn.click();
                        }, 1500);
                    }

                    setTimeout(function() {
                        alerta.style.transition = 'opacity 0.5s ease';
                        alerta.style.opacity = '0';
                        setTimeout(function() { alerta.remove(); }, 500);
                    }, 5000);
                });
            });
        </script>
    </main>
</x-app-layout>