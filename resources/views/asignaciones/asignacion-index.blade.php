<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        /* ESTILO ALERTAS (VERDE PASTEL) */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }
        
        /* ESTILO ALERTAS ERROR (ROJO SUAVE) */
        .alert-danger { background-color: #f8d7da !important; color: #842029 !important; border-color: #f5c2c7 !important; }

        /* Estilos para inputs */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }

        /* Clase para inputs "delgados" */
        .compact-filter { width: auto; min-width: 140px; max-width: 180px; }

        /* Badges de Estado del Nicho */
        .badge-disponible { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
        .badge-ocupado { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
        .badge-lleno { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        
        /* Botones de acción pequeños */
        .btn-action { margin-right: 4px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Asignaciones</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $nichos->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administra la ocupación de nichos, socios y fallecidos.</p>
                </div>

                {{-- Botón Nuevo (Abre Modal) --}}
                <button type="button" class="btn btn-success px-4 open-modal" 
                        style="height: fit-content;"
                        data-url="{{ route('asignaciones.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nueva Asignación
                </button>
            </div>

            {{-- 3. ALERTAS --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show alert-temporal mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show alert-temporal mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 4. FORMULARIO Y FILTROS --}}
            <form action="{{ route('asignaciones.index') }}" method="GET" id="filterForm">
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- Botón Generar Reporte (Estilo Dropdown Azul) --}}
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto" 
                                style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" 
                                type="button" id="dropdownGenerate" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-print me-2"></i> Reportes
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownGenerate">
                            <li>
                                <a href="{{ route('asignaciones.pdf.general') }}" target="_blank" class="dropdown-item">
                                    <i class="fas fa-list-alt text-primary me-2"></i> Reporte General
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('asignaciones.pdf.exhumados') }}" target="_blank" class="dropdown-item">
                                    <i class="fas fa-person-digging text-dark me-2"></i> Ver Exhumados
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Filtros (Compactos) --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        
                        {{-- Filtro de Estado (Reemplaza al de Comunidad) --}}
                        <select name="estado" class="form-select form-select-sm compact-filter ps-2" onchange="document.getElementById('filterForm').submit()">
                            <option value="">Todos los Estados</option>
                            <option value="OCUPADO" @selected(request('estado') == 'OCUPADO')>Ocupados</option>
                            <option value="LLENO" @selected(request('estado') == 'LLENO')>Llenos</option>
                            <option value="DISPONIBLE" @selected(request('estado') == 'DISPONIBLE')>Disponibles</option>
                        </select>

                        {{-- Buscador --}}
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-0 ps-1 shadow-none" 
                                   placeholder="Socio, Fallecido, Nicho..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </form>

            {{-- 5. TABLA --}}
            <div class="card shadow-sm border">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Nicho</th>
                                    <th style="text-align: left; padding-left: 15px;">Fallecido(s)</th>
                                    <th style="text-align: left; padding-left: 15px;">Socio Responsable</th>
                                    <th>Estado</th>
                                    <th>Ocupación</th>
                                    <th style="width:160px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($nichos as $nicho)
                                    <tr>
                                        {{-- Índice --}}
                                        <td class="fw-bold text-secondary">{{ $nichos->firstItem() + $loop->index }}</td>
                                        
                                        {{-- Nicho --}}
                                        <td class="fw-bold text-dark">
                                            {{ $nicho->codigo }}
                                            <div class="text-xs font-weight-normal text-secondary">{{ $nicho->bloque->descripcion ?? '' }}</div>
                                        </td>
                                        
                                        {{-- Fallecidos (Lista) --}}
                                        <td class="text-start ps-3">
                                            @php 
                                                $ocupantes = $nicho->fallecidos->where('pivot.fecha_exhumacion', null); 
                                            @endphp
                                            @forelse($ocupantes as $f)
                                                <div class="text-sm"><i class="text-xs me-1 text-muted"></i> {{ $f->apellidos }} {{ $f->nombres }}</div>
                                            @empty
                                                <span class="text-muted small fst-italic">-- Sin ocupantes --</span>
                                            @endforelse
                                        </td>
                                        
                                        {{-- Socio --}}
                                        <td class="text-start ps-3">
                                            @if($nicho->socios->isNotEmpty())
                                                {{ $nicho->socios->first()->nombre_completo }}
                                                <div class="text-xs text-muted">{{ $nicho->socios->first()->cedula }}</div>
                                            @else
                                                <span class="badge bg-secondary">Sin Asignar</span>
                                            @endif
                                        </td>
                                        
                                        {{-- Estado --}}
                                        <td>
                                            @php
                                                $clase = match($nicho->estado) {
                                                    'DISPONIBLE' => 'badge-disponible',
                                                    'OCUPADO' => 'badge-ocupado',
                                                    'LLENO' => 'badge-lleno',
                                                    default => 'bg-secondary text-white'
                                                };
                                            @endphp
                                            <span class="badge {{ $clase }}">{{ $nicho->estado }}</span>
                                        </td>

                                        {{-- Ocupación --}}
                                        <td>
                                            <span class="fw-bold {{ $ocupantes->count() >= 3 ? 'text-danger' : 'text-dark' }}">
                                                {{ $ocupantes->count() }} / 3
                                            </span>
                                        </td>
                                        
                                        {{-- Acciones --}}
                                        <td>
                                            {{-- SHOW (Ver) --}}
                                            <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal" 
                                                    data-url="{{ route('asignaciones.show', $nicho->id) }}" title="Ver Detalles">
                                                <i class="fa fa-eye"></i>
                                            </button>

                                            @if($ocupantes->count() > 0 || $nicho->socios->count() > 0)
                                                {{-- EDIT (Corregir) --}}
                                                <button type="button" class="btn btn-sm btn-warning mb-0 btn-action open-modal" 
                                                        data-url="{{ route('asignaciones.edit', $nicho->id) }}" title="Editar/Corregir">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            @endif

                                            {{-- EXHUMAR (Solo si hay ocupantes) --}}
                                            @if($ocupantes->count() > 0)
                                                {{-- OJO: Este botón abre la VISTA SEPARADA que pediste (exhumarForm) --}}
                                                <button type="button" class="btn btn-sm btn-dark mb-0 btn-action open-modal" 
                                                        data-url="{{ route('asignaciones.exhumarForm', $nicho->id) }}" 
                                                        title="Registrar Exhumación">
                                                    <i class="fa-solid fa-person-digging"></i>
                                                </button>
                                            @endif

                                            {{-- ELIMINAR (Solo si hay historial) --}}
                                            @if($nicho->fallecidos->count() > 0)
                                                {{-- Nota: Mandamos el ID del último fallecido para la ruta destroy, 
                                                     pero el usuario debe confirmar. Es para borrar errores de dedo. --}}
                                                <button type="button" class="btn btn-sm btn-danger mb-0 btn-action js-delete-btn"
                                                        data-url="{{ route('asignacion.destroy', [$nicho->id, $nicho->fallecidos->last()->id]) }}"
                                                        data-item="Asignación del Nicho {{ $nicho->codigo }}" 
                                                        title="Eliminar Registro (Error)">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron asignaciones registradas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">{{ $nichos->appends(request()->query())->links() }}</div>
                </div>
            </div>

            {{-- Formulario oculto para eliminar --}}
            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered"><div class="modal-content"></div></div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Alertas
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 3000);

                // Modal AJAX
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modalEl.querySelector('.modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(this.getAttribute('data-url')).then(r => r.text()).then(h => { modalEl.querySelector('.modal-content').innerHTML = h; });
                    });
                });

                // SweetAlert Eliminar (Tu lógica exacta)
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: '¿Eliminar Registro?', 
                            html: `¿Deseas eliminar <b>"${this.getAttribute('data-item')}"</b>?<br><small class="text-danger">Esta acción borrará el historial de asignación.</small>`, 
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
        </script>
    </main>
</x-app-layout>