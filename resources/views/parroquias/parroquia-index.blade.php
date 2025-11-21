<x-app-layout>
    <style>
        /* 1. ESTILO ALERTAS (VERDE PASTEL) */
        .alert-success {
            background-color: #e4f4db !important;
            color: #708736 !important;
            border-color: #e4f4db !important;
            font-weight: 400 !important;
            font-size: 14px !important;
        }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }

        /* Ajuste para inputs de búsqueda y filtro */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus {
            border-color: #5ea6f7;
            box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25);
        }

        /* Clase para hacer los inputs "delgados" como botones */
        .compact-filter {
            width: auto; 
            min-width: 140px; 
            max-width: 180px;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 1. ENCABEZADO Y BOTÓN NUEVO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Parroquias</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $parroquias->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Aquí puedes gestionar y generar reportes de las parroquias.</p>
                </div>

                <button type="button" class="btn btn-success px-4" 
                        style="height: fit-content;"
                        data-bs-toggle="modal" data-bs-target="#createParroquiaModal">
                    <i class="fa-solid fa-plus me-2"></i> Nueva Parroquia
                </button>
            </div>

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

            {{-- 
                CORRECCIÓN AQUÍ: 
                Se descomentó la ruta. Asegúrate de tener la ruta 'parroquias.reports' en web.php 
            --}}
            <form action="{{ route('parroquias.reports') }}" method="POST" id="reportForm">
                @csrf

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- BOTÓN GENERAR REPORTE --}}
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
                        <select id="cantonFilter" class="form-select form-select-sm compact-filter ps-2" title="Filtrar por Cantón">
                            <option value="">Todo Cantón</option>
                            @foreach($cantones as $c)
                                <option value="{{ $c->id }}" @selected(request('canton_id') == $c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>

                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" 
                                   placeholder="Buscar..." id="searchInput" 
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;"></th>
                                        <th style="width: 50px;">#</th>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Cantón</th>
                                        <th style="width:140px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($parroquias as $p)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $p->id }}" style="cursor: pointer;"></td>
                                            <td class="fw-bold text-secondary">{{ $parroquias->firstItem() + $loop->index }}</td>
                                            <td class="fw-bold text-dark">{{ $p->codigo }}</td>
                                            <td class="text-start ps-4">{{ $p->nombre }}</td>
                                            <td><span class="badge border text-dark bg-light">{{ $p->canton->nombre }}</span></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning mb-0 me-1" 
                                                        data-bs-toggle="modal" data-bs-target="#editParroquiaModal"
                                                        data-id="{{ $p->id }}" data-nombre="{{ $p->nombre }}"
                                                        data-canton="{{ $p->canton_id }}" data-codigo="{{ $p->codigo }}">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger mb-0 js-delete-btn"
                                                        data-url="{{ route('parroquias.destroy', $p) }}"
                                                        data-item="{{ $p->nombre }}">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron parroquias.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">{{ $parroquias->links() }}</div>
                    </div>
                </div>
            </form>
            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- Modales Crear/Editar --}}
        <div class="modal fade" id="createParroquiaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title text-white">Nueva Parroquia</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('parroquias.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info py-2 mb-3 text-xs"><i class="fas fa-info-circle me-1"></i> Código automático (Ej: PAR001).</div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Cantón</label>
                                <select name="canton_id" class="form-select" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach($cantones as $c) <option value="{{ $c->id }}">{{ $c->nombre }}</option> @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre Parroquia</label>
                                <input type="text" name="nombre" class="form-control" required placeholder="Ej: San Pablo">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editParroquiaModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title font-weight-bold" id="editModalTitle">Editar Parroquia</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editParroquiaForm" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3"><label class="form-label fw-bold text-muted">Código</label><input type="text" id="editCodigo" class="form-control bg-light" readonly></div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Cantón</label>
                                <select name="canton_id" id="editCanton" class="form-select" required>
                                    @foreach($cantones as $c) <option value="{{ $c->id }}">{{ $c->nombre }}</option> @endforeach
                                </select>
                            </div>
                            <div class="mb-3"><label class="form-label fw-bold">Nombre</label><input type="text" name="nombre" id="editNombre" class="form-control" required></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-app.footer />

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                setTimeout(() => { document.querySelectorAll('.alert-temporal').forEach(alert => { alert.style.transition = "opacity 0.5s"; alert.style.opacity = 0; setTimeout(() => alert.remove(), 500); }); }, 3000);
                const searchInput = document.getElementById('searchInput'); const cantonFilter = document.getElementById('cantonFilter');
                function applyFilters() { window.location.href = "{{ route('parroquias.index') }}?search=" + encodeURIComponent(searchInput.value) + "&canton_id=" + cantonFilter.value; }
                searchInput.addEventListener('keypress', function (e) { if (e.key === 'Enter') { e.preventDefault(); applyFilters(); } });
                cantonFilter.addEventListener('change', applyFilters);
                var editModal = document.getElementById('editParroquiaModal');
                editModal.addEventListener('show.bs.modal', function (event) {
                    var b = event.relatedTarget;
                    document.getElementById('editNombre').value = b.getAttribute('data-nombre');
                    document.getElementById('editCanton').value = b.getAttribute('data-canton');
                    document.getElementById('editCodigo').value = b.getAttribute('data-codigo');
                    document.getElementById('editParroquiaForm').action = "{{ route('parroquias.update', 'ID_PLACEHOLDER') }}".replace('ID_PLACEHOLDER', b.getAttribute('data-id'));
                });
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if(confirm('¿Eliminar ' + this.getAttribute('data-item') + '?')) { const f = document.getElementById('deleteForm'); f.action = this.getAttribute('data-url'); f.submit(); }
                    });
                });
            });
            function toggleSelectAll() { const c = document.getElementById('selectAll').checked; document.querySelectorAll('input[name="ids[]"]').forEach(x => x.checked = c); }
        </script>
    </main>
</x-app-layout>