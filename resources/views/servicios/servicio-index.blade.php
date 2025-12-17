<x-app-layout>
    {{-- 1. ESTILOS (Idénticos a Socios) --}}
    <style>
        /* ESTILO ALERTAS (VERDE PASTEL) */
        .alert-success {
            background-color: #e4f4db !important;
            color: #708736 !important;
            border-color: #e4f4db !important;
            font-weight: 400 !important;
            font-size: 14px !important;
        }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }

        /* Estilos para input groups y focus */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus {
            border-color: #5ea6f7;
            box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25);
        }

        /* Clase para inputs "delgados" como botones */
        .compact-filter {
            width: auto; 
            min-width: 140px; 
            max-width: 250px;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO Y BOTÓN NUEVO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Servicios</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $servicios->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Aquí puedes gestionar y generar reportes de los servicios.</p>
                </div>

                {{-- Botón Nuevo (Abre Modal) --}}
                <button type="button" class="btn btn-success px-4 open-modal" 
                        style="height: fit-content;"
                        data-url="{{ route('servicios.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Servicio
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
                <div class="alert alert-danger text-white alert-dismissible fade show alert-temporal mb-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 4. FORMULARIO DE REPORTES Y FILTROS --}}
            <form action="{{ route('servicios.reports') }}" method="POST" id="reportForm">
                @csrf

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- BOTÓN GENERAR REPORTE (Azul) --}}
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto" 
                                style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" 
                                type="button" id="dropdownGenerate" data-bs-toggle="dropdown" aria-expanded="false">
                            Generar Reporte
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownGenerate">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    {{-- FILTROS --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" 
                                   placeholder="Buscar servicio..." id="searchInput" 
                                   value="{{ request('q') }}">
                        </div>
                    </div>
                </div>

                {{-- 5. TABLA --}}
                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;"></th>
                                        <th style="width: 50px;">#</th>
                                        <th>Código</th>
                                        <th class="text-start ps-4">Nombre</th>
                                        <th class="text-start ps-4">Descripción</th>
                                        <th>Valor ($)</th>
                                        <th style="width:140px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($servicios as $s)
                                        <tr>
                                            {{-- Checkbox --}}
                                            <td><input type="checkbox" name="ids[]" value="{{ $s->id }}" class="check-item" style="cursor: pointer;"></td>
                                            
                                            {{-- Índice --}}
                                            <td class="fw-bold text-secondary">{{ $servicios->firstItem() + $loop->index }}</td>
                                            
                                            {{-- Código --}}
                                            <td class="fw-bold text-dark">{{ $s->codigo }}</td>
                                            
                                            {{-- Nombre --}}
                                            <td class="text-start ps-4">{{ $s->nombre }}</td>
                                            
                                            {{-- Descripción (Truncada) --}}
                                            <td class="text-start ps-4 text-muted small">
                                                {{ Str::limit($s->descripcion, 40) ?? '—' }}
                                            </td>

                                            {{-- Valor --}}
                                            <td>
                                                @if($s->valor)
                                                    <span class="badge bg-light text-dark border">$ {{ number_format($s->valor, 2) }}</span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            
                                            {{-- Acciones --}}
                                            <td>
                                                {{-- Ver --}}
                                                <button type="button" class="btn btn-sm btn-info mb-0 me-1 open-modal" 
                                                        data-url="{{ route('servicios.show', $s) }}" title="Ver">
                                                    <i class="fa fa-eye"></i>
                                                </button>

                                                {{-- Editar --}}
                                                <button type="button" class="btn btn-sm btn-warning mb-0 me-1 open-modal" 
                                                        data-url="{{ route('servicios.edit', $s) }}" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>

                                                {{-- Eliminar --}}
                                                <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn"
                                                        data-url="{{ route('servicios.destroy', $s) }}"
                                                        data-item="{{ $s->nombre }}" title="Eliminar">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron servicios registrados.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">{{ $servicios->links() }}</div>
                    </div>
                </div>
            </form>

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
                // Alerta Temporal
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; 
                        alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 3000);

                // Filtros
                const searchInput = document.getElementById('searchInput'); 
                
                function applyFilters() { 
                    window.location.href = "{{ route('servicios.index') }}?q=" + encodeURIComponent(searchInput.value); 
                }
                
                if(searchInput) searchInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') { e.preventDefault(); applyFilters(); } });

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

                // SweetAlert Eliminar
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: '¿Eliminar Servicio?', 
                            html: `¿Deseas eliminar el servicio <b>"${this.getAttribute('data-item')}"</b>?`, 
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

            function toggleSelectAll() { 
                const c = document.getElementById('selectAll').checked; 
                document.querySelectorAll('.check-item').forEach(x => x.checked = c); 
            }
        </script>
    </main>
</x-app-layout>