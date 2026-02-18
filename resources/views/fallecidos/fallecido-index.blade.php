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
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
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
                
                {{-- Inputs ocultos para asegurar que si no se selecciona nada, se reporte según el filtro actual --}}
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

                    {{-- Lado Derecho: Filtros (Select + Buscador) --}}
                    {{-- Nota: Estos inputs NO tienen atributo name para no enviarse en el POST del reporte y ensuciar la request, 
                         se manejan por JS para la redirección GET, o se usan los inputs ocultos arriba --}}
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

                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i
                                    class="fas fa-search"></i></span>
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
                                <tbody>
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
                        @if($fallecidos->hasPages())
                            <div class="mt-3 px-3 d-flex justify-content-end">
                                {{ $fallecidos->appends(request()->query())->links() }}
                            </div>
                        @endif
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
                // 1. Alertas
                setTimeout(() => {
                    document.querySelectorAll('.alert-temporal').forEach(alert => {
                        alert.style.transition = "opacity 0.5s"; alert.style.opacity = 0;
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 3000);

                // 2. Filtros (GET Redirect)
                const searchInput = document.getElementById('searchInput');
                const comunidadFilter = document.getElementById('comunidadFilter');
                const mesFilter = document.getElementById('mesFilter');
                const anioFilter = document.getElementById('anioFilter');

                function applyFilters() {
                    const params = new URLSearchParams();
                    
                    if(searchInput.value) params.append('search', searchInput.value);
                    if(comunidadFilter.value) params.append('comunidad_id', comunidadFilter.value);
                    if(mesFilter.value) params.append('mes', mesFilter.value);
                    if(anioFilter.value) params.append('anio', anioFilter.value);

                    window.location.href = "{{ route('fallecidos.index') }}?" + params.toString();
                }

                if (searchInput) {
                    searchInput.addEventListener('keypress', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault(); 
                            applyFilters();
                        }
                    });
                }

                if (comunidadFilter) comunidadFilter.addEventListener('change', applyFilters);
                if (mesFilter) mesFilter.addEventListener('change', applyFilters);
                if (anioFilter) anioFilter.addEventListener('change', applyFilters);

                // 3. Modal AJAX
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault(); // Evita que botones dentro del form envíen el reporte
                        modalEl.querySelector('.modal-content').innerHTML = `
                            <div class="p-5 text-center">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-2 text-secondary">Cargando...</p>
                            </div>`;
                        modal.show();
                        fetch(this.getAttribute('data-url'))
                            .then(r => r.text())
                            .then(h => { 
                                modalEl.querySelector('.modal-content').innerHTML = h;
                                // 🔥 Ejecutar scripts que vienen dentro del HTML cargado
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
                });

                // 4. Delete SweetAlert
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault(); // Evita submit del form principal
                        Swal.fire({
                            title: '¿Eliminar Registro?',
                            html: `¿Deseas eliminar a <b>"${this.getAttribute('data-item')}"</b>?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((r) => {
                            if (r.isConfirmed) {
                                const f = document.getElementById('deleteForm');
                                f.action = this.getAttribute('data-url');
                                f.submit();
                            }
                        });
                    });
                });

                // 5. Select All (Lógica corregida)
                const selectAll = document.getElementById('selectAll');
                if(selectAll) {
                    selectAll.addEventListener('change', function() {
                        const checked = this.checked;
                        document.querySelectorAll('.check-item').forEach(checkbox => {
                            checkbox.checked = checked;
                        });
                    });
                }

                // 🔔 6. VALIDACIÓN POR PESTAÑAS EN MODALES
                // Usa delegación de eventos para funcionar con contenido cargado por AJAX
                modalEl.addEventListener('submit', function(e) {
                    const form = e.target;
                    if (!form.matches('form')) return;

                    // Buscar campos required vacíos por pestaña
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

                    // Si no hay campos vacíos, dejar enviar
                    if (vaciosPersonal.length === 0 && vaciosDetalles.length === 0) return;

                    e.preventDefault();

                    // Quitar alerta anterior
                    const prev = form.querySelector('.alerta-validacion');
                    if (prev) prev.remove();

                    // Detectar pestaña activa
                    const tabPersonalActivo = modalEl.querySelector('#personal.show.active, #edit-personal.show.active');
                    const tabDetallesActivo = modalEl.querySelector('#detalles.show.active, #edit-detalles.show.active');

                    let msg = '';
                    let irAPestana = null;

                    if (vaciosPersonal.length > 0 && vaciosDetalles.length > 0) {
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Faltan campos obligatorios en <strong>Personal</strong> y en <strong>Detalles y Notas</strong>. Por favor complételos.';
                    } else if (vaciosDetalles.length > 0 && tabPersonalActivo) {
                        // Estoy en Personal, faltan datos en Detalles
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Vaya al apartado <strong>Detalles y Notas</strong> y complete los campos obligatorios.';
                        irAPestana = '[data-bs-target="#detalles"], [data-bs-target="#edit-detalles"]';
                    } else if (vaciosPersonal.length > 0 && tabDetallesActivo) {
                        // Estoy en Detalles, faltan datos en Personal
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Vaya al apartado <strong>Personal</strong> y complete los campos obligatorios.';
                        irAPestana = '[data-bs-target="#personal"], [data-bs-target="#edit-personal"]';
                    } else if (vaciosPersonal.length > 0) {
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Complete los campos obligatorios en esta pestaña.';
                    } else {
                        msg = '<i class="fas fa-exclamation-triangle me-2"></i> Complete los campos obligatorios en esta pestaña.';
                    }

                    // Crear alerta warning
                    const alerta = document.createElement('div');
                    alerta.className = 'alert alert-warning py-2 mb-0 mt-2 alerta-validacion d-flex align-items-center';
                    alerta.style.cssText = 'font-size: 0.85rem; border-left: 4px solid #ffc107;';
                    alerta.innerHTML = msg;

                    const body = form.querySelector('.modal-body');
                    if (body) body.appendChild(alerta);

                    // Navegar a la otra pestaña si corresponde
                    if (irAPestana) {
                        setTimeout(function() {
                            const btn = modalEl.querySelector(irAPestana);
                            if (btn) btn.click();
                        }, 1500); // Espera 1.5s para que lean el mensaje antes de cambiar
                    }

                    // Auto-ocultar en 5 segundos
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