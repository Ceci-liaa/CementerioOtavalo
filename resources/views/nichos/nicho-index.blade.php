<x-app-layout>
    {{-- 1. ESTILOS (Estandarizados) --}}
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
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Nichos</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $nichos->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administración de espacios, capacidad y estados físicos.</p>
                </div>

                {{-- Botón Nuevo --}}
                <button type="button" class="btn btn-success px-4 open-modal" style="height: fit-content;"
                    data-url="{{ route('nichos.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Nicho
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
            </div>

            {{-- INICIO DEL FORMULARIO DE REPORTE (Abarca Filtros y Tabla) --}}
            <form action="{{ route('nichos.reports') }}" method="POST" id="reportForm">
                @csrf
                
                {{-- Inputs ocultos para filtros actuales en el reporte --}}
                <input type="hidden" name="current_q" value="{{ request('q') }}">
                <input type="hidden" name="current_bloque" value="{{ request('bloque_id') }}">

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
                        {{-- Filtro de Bloque --}}
                        <select id="bloqueFilter" class="form-select form-select-sm compact-filter ps-2">
                            <option value="">Todos los bloques</option>
                            @foreach($bloques as $b)
                                <option value="{{ $b->id }}" @selected(request('bloque_id') == $b->id)>{{ $b->nombre }}</option>
                            @endforeach
                        </select>

                        {{-- Filtro de Estado --}}
                        <select id="estadoFilter" class="form-select form-select-sm compact-filter ps-2">
                            <option value="">Todos los estados</option>
                            <option value="BUENO" @selected(request('estado') == 'BUENO')>Bueno</option>
                            <option value="MANTENIMIENTO" @selected(request('estado') == 'MANTENIMIENTO')>Mantenimiento</option>
                            <option value="MALO" @selected(request('estado') == 'MALO')>Malo</option>
                            <option value="ABANDONADO" @selected(request('estado') == 'ABANDONADO')>Abandonado</option>
                        </select>

                        {{-- Buscador --}}
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i
                                    class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" placeholder="Código, Responsable..."
                                id="searchInput" value="{{ request('q') }}">
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
                                        <th class="opacity-10">Bloque</th>
                                        <th class="opacity-10">Clase</th>
                                        <th class="opacity-10">Tipo</th>
                                        <th class="opacity-10">Ocupación</th>
                                        <th class="opacity-10">Estado</th>
                                        <th class="opacity-10">Responsable</th>
                                        <th class="opacity-10">Disp.</th>
                                        <th class="opacity-10" style="width:170px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($nichos as $n)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ids[]" value="{{ $n->id }}" class="check-item"
                                                    style="cursor: pointer;">
                                            </td>

                                            <td class="text-sm fw-bold text-secondary">
                                                {{ $nichos->firstItem() + $loop->index }}
                                            </td>

                                            <td class="fw-bold text-dark">{{ $n->codigo }}</td>

                                            <td>
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="fw-bold text-xs">{{ $n->bloque?->codigo }}</span>
                                                    <small class="text-muted" style="font-size: 0.65rem;">{{ Str::limit($n->bloque?->nombre, 12) }}</small>
                                                </div>
                                            </td>

                                            <td>
                                                @if($n->clase_nicho == 'TIERRA')
                                                    <span class="badge border" style="background-color: #f0f2f5; color: #5974a2;">Tierra</span>
                                                @else
                                                    <span class="badge border bg-light text-dark">Bóveda</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($n->tipo_nicho === 'PROPIO')
                                                    <span class="badge bg-info text-dark" style="font-size: 0.65rem;">PROPIO</span>
                                                @else
                                                    <span class="badge bg-primary" style="font-size: 0.65rem;">COMPARTIDO</span>
                                                @endif
                                            </td>

                                            <td>
                                                <span class="fw-bold text-xs {{ $n->ocupacion >= $n->capacidad ? 'text-danger' : 'text-success' }}">
                                                    {{ $n->ocupacion }} / {{ $n->capacidad }}
                                                </span>
                                            </td>

                                            <td>
                                                @switch($n->estado)
                                                    @case('BUENO') <span class="badge bg-success" style="font-size: 0.65rem;">Bueno</span> @break
                                                    @case('MANTENIMIENTO') <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Mant.</span> @break
                                                    @case('MALO') <span class="badge bg-danger" style="font-size: 0.65rem;">Malo</span> @break
                                                    @case('ABANDONADO') <span class="badge bg-secondary" style="font-size: 0.65rem;">Aband.</span> @break
                                                    @default <span class="badge bg-light text-dark" style="font-size: 0.65rem;">{{ $n->estado }}</span>
                                                @endswitch
                                            </td>

                                            {{-- Responsable (Socio) --}}
                                            <td class="text-sm">
                                                @if($n->socio)
                                                    <span class="fw-bold text-dark">{{ $n->socio->apellidos }} {{ $n->socio->nombres }}</span>
                                                @else
                                                    <span class="text-muted fst-italic small">Sin asignar</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($n->disponible)
                                                    <i class="fas fa-check-circle text-success" title="Disponible"></i>
                                                @else
                                                    <i class="fas fa-times-circle text-secondary" title="No disponible"></i>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="d-flex justify-content-center align-items-center">
                                                    {{-- Botón QR DIRECTO (Sin dropdown) --}}
                                                    <a href="{{ route('nichos.qr', ['nicho' => $n->id, 'mode' => 'text']) }}" 
                                                       target="_blank" 
                                                       class="btn btn-sm btn-dark mb-0 btn-action" 
                                                       title="Ver QR">
                                                        <i class="fas fa-qrcode" style="font-size: 0.7rem;"></i>
                                                    </a>

                                                    {{-- Botón Ver --}}
                                                    <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal"
                                                        data-url="{{ route('nichos.show', $n->id) }}" title="Ver">
                                                        <i class="fa-solid fa-eye text-white" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    
                                                    {{-- Botón Editar --}}
                                                    <button type="button" class="btn btn-sm btn-warning mb-0 btn-action open-modal"
                                                        data-url="{{ route('nichos.edit', $n->id) }}" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    
                                                    {{-- Botón Eliminar --}}
                                                    <button type="button" class="btn btn-sm btn-danger mb-0 btn-action js-delete-btn"
                                                        data-url="{{ route('nichos.destroy', $n) }}"
                                                        data-item="{{ $n->codigo }}" title="Eliminar">
                                                        <i class="fa-solid fa-trash" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-5 text-muted">
                                                No se encontraron nichos registrados.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        @if($nichos->hasPages())
                            <div class="mt-3 px-3 d-flex justify-content-end">
                                {{ $nichos->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </form> {{-- FIN DEL FORMULARIO --}}

            {{-- Formulario de Eliminación --}}
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
                const bloqueFilter = document.getElementById('bloqueFilter');
                const estadoFilter = document.getElementById('estadoFilter');

                function applyFilters() {
                    const qValue = encodeURIComponent(searchInput.value);
                    const bloqueValue = encodeURIComponent(bloqueFilter.value);
                    const estadoValue = encodeURIComponent(estadoFilter.value);
                    window.location.href = "{{ route('nichos.index') }}?q=" + qValue + "&bloque_id=" + bloqueValue + "&estado=" + estadoValue;
                }

                if (searchInput) {
                    searchInput.addEventListener('keypress', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault(); 
                            applyFilters();
                        }
                    });
                }

                if (bloqueFilter) {
                    bloqueFilter.addEventListener('change', applyFilters);
                }

                if (estadoFilter) {
                    estadoFilter.addEventListener('change', applyFilters);
                }

                // 3. Modal AJAX
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
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
                                // Ejecutar scripts cargados dinámicamente
                                modalEl.querySelectorAll('.modal-content script').forEach(oldScript => {
                                    const newScript = document.createElement('script');
                                    newScript.textContent = oldScript.textContent;
                                    oldScript.parentNode.replaceChild(newScript, oldScript);
                                });
                            });
                    });
                });

                // 4. Delete SweetAlert
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Eliminar Nicho?',
                            html: `¿Deseas eliminar el nicho <b>"${this.getAttribute('data-item')}"</b>?`,
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

                // 5. Select All
                const selectAll = document.getElementById('selectAll');
                if(selectAll) {
                    selectAll.addEventListener('change', function() {
                        const checked = this.checked;
                        document.querySelectorAll('.check-item').forEach(checkbox => {
                            checkbox.checked = checked;
                        });
                    });
                }
            });
        </script>
    </main>
</x-app-layout>