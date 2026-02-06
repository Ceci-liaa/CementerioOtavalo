<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        /* ALERTAS */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; font-size: 14px !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }
        
        .alert-danger { background-color: #fde1e1 !important; color: #cf304a !important; border-color: #fde1e1 !important; font-size: 14px !important; }
        .alert-danger .btn-close { filter: none !important; opacity: 0.5; color: #cf304a; }
        
        /* Alerta Candidatos (Amarillo) */
        .alert-warning-custom { background-color: #fff3cd !important; border-color: #ffecb5 !important; color: #664d03 !important; }

        /* INPUTS Y FILTROS */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 200px; } 

        /* TABLA */
        .table thead th {
            font-size: 14px !important;    
            text-transform: uppercase;    
            letter-spacing: 0.05rem;      
            font-weight: 700 !important;  
            padding-top: 15px !important; 
            padding-bottom: 15px !important; 
        }
        
        .btn-action { margin-right: 4px; }
        .candidate-list-item:hover { background-color: rgba(0,0,0,0.03); border-radius: 4px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">

            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Socios</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $socios->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Gestión del padrón de socios, beneficios y nichos asignados.</p>
                </div>

                {{-- Botón Nuevo --}}
                <button type="button" class="btn btn-success px-4 open-modal" style="height: fit-content;"
                    data-url="{{ route('socios.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Socio
                </button>
            </div>

            {{-- 3. ZONA DE ALERTAS --}}
            <div class="mb-3">
                {{-- Éxito --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show alert-temporal mb-2">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Error General --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal mb-2">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Errores de Validación (Formulario) --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal mb-2">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>No se pudo guardar:</strong>
                        </div>
                        <ul class="mb-0 ps-4 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>

            {{-- 4. ALERTA DE CANDIDATOS A EXONERACIÓN --}}
            @if(isset($candidatos) && $candidatos->isNotEmpty())
                <div class="alert alert-warning-custom mb-4 shadow-sm rounded-3 border">
                    <div class="d-flex align-items-start">
                        <div class="me-3 mt-1"><i class="fas fa-bell fa-lg text-warning"></i></div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading fw-bold mb-1" style="font-size: 0.95rem;">
                                <i class="fas fa-exclamation-triangle me-1"></i> Atención: Socios Elegibles para Exoneración
                            </h6>
                            <p class="mb-2 small opacity-8">
                                Estos socios han cumplido 75 años. Verifique su historial y actualice su estado si corresponde.
                            </p>
                            <div class="mt-2 border-top border-warning pt-2" style="border-color: rgba(102, 77, 3, 0.2) !important;">
                                <ul class="list-unstyled mb-0 row">
                                    @foreach($candidatos as $c)
                                        <li class="col-md-6 mb-1 candidate-list-item p-1 d-flex justify-content-between align-items-center">
                                            <span class="text-sm text-dark">
                                                • <strong>{{ $c->apellidos }} {{ $c->nombres }}</strong>
                                                <span class="text-muted ms-1">({{ $c->edad }} años)</span>
                                            </span>
                                            <button type="button" class="btn btn-sm btn-link text-primary fw-bold p-0 m-0 open-modal"
                                                data-url="{{ route('socios.edit', $c) }}" style="text-decoration: underline; font-size: 0.8rem;">
                                                Gestionar
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            {{-- 5. BARRA DE HERRAMIENTAS (Reportes + Filtros) --}}
            <form action="{{ route('socios.reports') }}" method="POST" id="reportForm">
                @csrf
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- Reportes --}}
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto"
                            style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" type="button"
                            data-bs-toggle="dropdown">
                            Generar Reporte
                        </button>
                        <ul class="dropdown-menu">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    {{-- Filtros --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        {{-- Select Comunidad --}}
                        <select id="comunidadFilter" class="form-select form-select-sm compact-filter ps-2">
                            <option value="">Toda Comunidad</option>
                            @foreach($comunidades as $c)
                                <option value="{{ $c->id }}" @selected(request('comunidad_id') == $c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>

                        {{-- Buscador --}}
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" placeholder="Buscar..."
                                id="searchInput" value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </form>

            {{-- 6. TABLA DE SOCIOS --}}
            <div class="card shadow-sm border">
                <div class="card-body p-0 pb-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="opacity-10" style="width: 40px;">
                                        <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;">
                                    </th>
                                    <th class="opacity-10" style="width: 50px;">#</th>
                                    <th class="opacity-10" style="width: 10%;">Código</th>
                                    <th class="opacity-10" style="width: 12%;">Cédula</th>
                                    <th class="opacity-10 text-start ps-4">Nombre Completo</th>
                                    <th class="opacity-10">Comunidad</th>
                                    <th class="opacity-10">Edad</th>
                                    <th class="opacity-10 text-center" style="width: 140px;">Nichos</th>
                                    <th class="opacity-10" style="width: 130px;">Estado</th>
                                    <th class="opacity-10" style="width:140px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($socios as $s)
                                    <tr>
                                        <td>
                                            {{-- IMPORTANTE: form="reportForm" para que el checkbox funcione con el reporte --}}
                                            <input type="checkbox" name="ids[]" value="{{ $s->id }}" class="check-item" form="reportForm" style="cursor: pointer;">
                                        </td>
                                        
                                        <td class="text-sm fw-bold text-secondary">
                                            {{ $socios->firstItem() + $loop->index }}
                                        </td>

                                        <td class="fw-bold text-dark">{{ $s->codigo }}</td>
                                        
                                        <td class="text-secondary text-sm">{{ $s->cedula }}</td>
                                        
                                        <td class="text-start ps-4">
                                            <span class="text-sm font-weight-bold text-dark">{{ $s->apellidos }} {{ $s->nombres }}</span>
                                        </td>

                                        <td>
                                            <span class="badge border" style="background-color: #f8f9fa; color: #343a40;">
                                                {{ $s->comunidad?->nombre ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <td class="text-sm">{{ $s->edad }} años</td>

                                        {{-- COLUMNA NICHOS --}}
                                        <td class="text-center align-middle">
                                            @if($s->total_nichos > 0)
                                                <div class="d-flex flex-column gap-1 align-items-center">
                                                    @if($s->propios_count > 0)
                                                        <span class="badge border text-dark bg-light w-100" style="font-weight: 600; font-size: 0.7rem; border-color: #ffecb5 !important; background-color: #fffbf0 !important;">
                                                            <i class="fas fa-crown text-warning me-1"></i> {{ $s->propios_count }} Propio(s)
                                                        </span>
                                                    @endif
                                                    
                                                    @if($s->compartidos_count > 0)
                                                        <span class="badge border text-dark bg-white w-100" style="font-weight: 600; font-size: 0.7rem; border-color: #b6effb !important; background-color: #f2fbfe !important;">
                                                            <i class="fas fa-users text-info me-1"></i> {{ $s->compartidos_count }} Comp.
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted text-xs">—</span>
                                            @endif
                                        </td>

                                        {{-- COLUMNA ESTADO --}}
                                        <td>
                                            @if($s->tipo_beneficio === 'exonerado')
                                                <span class="badge" style="background-color: #198754; color: white;">EXONERADO</span>
                                            @elseif($s->tipo_beneficio === 'con_subsidio')
                                                <span class="badge" style="background-color: #0d6efd; color: white;">CON SUBSIDIO</span>
                                            @else
                                                <span class="badge border" style="background-color: #e9ecef; color: #495057;">SIN SUBSIDIO</span>
                                            @endif
                                        </td>

                                        {{-- COLUMNA ACCIONES --}}
                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal"
                                                    data-url="{{ route('socios.show', $s) }}" title="Ver Detalles">
                                                    <i class="fa fa-eye text-white" style="font-size: 0.7rem;"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-warning mb-0 btn-action open-modal"
                                                    data-url="{{ route('socios.edit', $s) }}" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-danger mb-0 btn-action js-delete-btn"
                                                    data-url="{{ route('socios.destroy', $s) }}"
                                                    data-item="{{ $s->apellidos }} {{ $s->nombres }}" title="Eliminar">
                                                    <i class="fa-solid fa-trash" style="font-size: 0.7rem;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-5 text-muted">
                                            No se encontraron socios registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 px-3 d-flex justify-content-end">
                        {{ $socios->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>

            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content"></div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Función para la lógica interna del modal (Beneficio -> Fecha Exoneración)
            function initModalLogic() {
                const selectBeneficio = document.getElementById('select_beneficio');
                const divFechaExo = document.getElementById('div_fecha_exo');
                
                if (selectBeneficio && divFechaExo) {
                    const toggle = () => {
                        if (selectBeneficio.value === 'exonerado') {
                            divFechaExo.style.display = 'block';
                            const input = divFechaExo.querySelector('input');
                            if (input && !input.value) input.focus();
                        } else {
                            divFechaExo.style.display = 'none';
                        }
                    };
                    selectBeneficio.addEventListener('change', toggle);
                    toggle(); // Ejecutar al inicio
                }
            }

            document.addEventListener("DOMContentLoaded", function () {
                // Alertas Temporales
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 3500);

                // Filtros
                const searchInput = document.getElementById('searchInput');
                const comunidadFilter = document.getElementById('comunidadFilter');

                function applyFilters() {
                    const searchValue = encodeURIComponent(searchInput.value);
                    const comunidadValue = encodeURIComponent(comunidadFilter.value);
                    window.location.href = "{{ route('socios.index') }}?search=" + searchValue + "&comunidad_id=" + comunidadValue;
                }

                if (searchInput) searchInput.addEventListener('keypress', e => { if (e.key === 'Enter') { e.preventDefault(); applyFilters(); } });
                if (comunidadFilter) comunidadFilter.addEventListener('change', applyFilters);

                // Modal
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                
                document.body.addEventListener('click', function (e) {
                    if (e.target.closest('.open-modal')) {
                        const btn = e.target.closest('.open-modal');
                        
                        // Loader simple
                        modalEl.querySelector('.modal-content').innerHTML = `
                            <div class="p-5 text-center">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-2 text-secondary">Cargando...</p>
                            </div>`;
                        
                        modal.show();
                        
                        // Carga de contenido
                        fetch(btn.getAttribute('data-url'))
                            .then(r => r.text())
                            .then(h => {
                                modalEl.querySelector('.modal-content').innerHTML = h;
                                initModalLogic(); // Activa la lógica del form (Selects, Fechas, etc)
                                
                                // Reinicializar TomSelect si se usa en el modal cargado
                                // (Si tienes scripts inline en el modal, se ejecutarán automáticamente)
                            });
                    }
                });

                // SweetAlert Eliminar
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        Swal.fire({
                            title: '¿Eliminar Socio?',
                            html: `¿Deseas eliminar al socio <b>"${this.getAttribute('data-item')}"</b>?<br><small class="text-muted">Esta acción no se puede deshacer.</small>`,
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
            });

            // Seleccionar todo (Checkboxes)
            function toggleSelectAll() {
                const c = document.getElementById('selectAll').checked;
                document.querySelectorAll('.check-item').forEach(x => x.checked = c);
            }
        </script>
    </main>
</x-app-layout>