<x-app-layout>
    {{-- 1. ESTILOS (Idénticos a Usuarios/Cantones) --}}
    <style>
        /* ALERTAS */
        .alert-success {
            background-color: #e4f4db !important;
            color: #708736 !important;
            border-color: #e4f4db !important;
            font-size: 14px !important;
        }

        .alert-success .btn-close {
            filter: none !important;
            opacity: 0.5;
            color: #708736;
        }

        .alert-success .btn-close:hover {
            opacity: 1;
        }

        .alert-danger {
            background-color: #fde1e1 !important;
            color: #cf304a !important;
            border-color: #fde1e1 !important;
            font-size: 14px !important;
        }

        .alert-danger .btn-close {
            filter: none !important;
            opacity: 0.5;
            color: #cf304a;
        }

        /* BUSCADOR Y FILTROS */
        .input-group-text {
            border-color: #dee2e6;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #5ea6f7;
            box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25);
        }

        .compact-filter {
            width: auto;
            min-width: 140px;
            max-width: 200px;
        }

        /* ESTILOS DE TABLA */
        .table thead th {
            font-size: 14px !important;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            font-weight: 700 !important;
            padding-top: 15px !important;
            padding-bottom: 15px !important;
        }

        .btn-action {
            margin-right: 4px;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">

            {{-- 2. ENCABEZADO --}}
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Parroquias</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $parroquias->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administra el catálogo de parroquias del sistema.</p>
                </div>

                {{-- Botón Nuevo (Arriba a la derecha) --}}
                <button type="button" class="btn btn-success px-4 open-modal" style="height: fit-content;"
                    data-url="{{ route('parroquias.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nueva Parroquia
                </button>
            </div>

            {{-- 3. ALERTAS --}}
            <div class="mb-3">
                {{-- A. Mensajes de Éxito (Verde) --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show alert-temporal" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- B. Mensajes de Error del Sistema / Base de Datos (Rojo) --}}
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                {{-- C. NUEVO: Mensajes de Error de Validación (Ej: Nombre duplicado) --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>No se pudo guardar:</strong>
                                <ul class="mb-0 ps-3" style="list-style-type: none; padding-left: 0;">
                                    @foreach ($errors->all() as $error)
                                        <li>- {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            {{-- 4. BARRA DE HERRAMIENTAS (Reportes + Filtros) --}}
            <form action="{{ route('parroquias.reports') }}" method="POST" id="reportForm">
                @csrf
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
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        {{-- Filtro de Cantón --}}
                        <select id="cantonFilter" class="form-select form-select-sm compact-filter ps-2">
                            <option value="">Todo Cantón</option>
                            @foreach($cantones as $c)
                                <option value="{{ $c->id }}" @selected(request('canton_id') == $c->id)>{{ $c->nombre }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Buscador --}}
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i
                                    class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-1 shadow-none" placeholder="Buscar..."
                                id="searchInput" value="{{ request('search') }}">
                        </div>
                    </div>
                </div>
            </form>

            {{-- 5. TABLA (Estilo Usuarios: Header Oscuro, Código Negrita, pb-2) --}}
            <div class="card shadow-sm border">
                <div class="card-body p-0 pb-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="opacity-10" style="width: 40px;">
                                        <input type="checkbox" id="selectAll" onclick="toggleSelectAll()"
                                            style="cursor: pointer;">
                                    </th>
                                    <th class="opacity-10" style="width: 50px;">#</th>
                                    <th class="opacity-10" style="width: 20%;">Código</th>
                                    <th class="opacity-10 text-start ps-4">Nombre</th>
                                    <th class="opacity-10">Cantón</th>
                                    <th class="opacity-10" style="width:180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($parroquias as $p)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="check-item"
                                                style="cursor: pointer;">
                                        </td>

                                        <td class="text-sm fw-bold text-secondary">
                                            {{ $parroquias->firstItem() + $loop->index }}
                                        </td>

                                        {{-- Código en Negrita (Estilo Usuarios) --}}
                                        <td class="fw-bold text-dark">{{ $p->codigo }}</td>

                                        <td class="text-start ps-4">
                                            <span class="text-sm font-weight-bold">{{ $p->nombre }}</span>
                                        </td>

                                        {{-- Cantón estilo Badge (Similar a Rol) --}}
                                        <td>
                                            <span class="badge border"
                                                style="background-color: #f8f9fa; color: #343a40; font-size: 0.75rem; font-weight: 600;">
                                                {{ $p->canton->nombre ?? 'N/A' }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-center">
                                                <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal"
                                                    data-url="{{ route('parroquias.show', $p->id) }}" title="Ver">
                                                    <i class="fa-solid fa-eye text-white" style="font-size: 0.7rem;"></i>
                                                </button>

                                                <button type="button"
                                                    class="btn btn-sm btn-warning mb-0 btn-action open-modal"
                                                    data-url="{{ route('parroquias.edit', $p->id) }}" title="Editar">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                </button>

                                                <button type="button"
                                                    class="btn btn-sm btn-danger mb-0 btn-action js-delete-btn"
                                                    data-url="{{ route('parroquias.destroy', $p) }}"
                                                    data-item="{{ $p->nombre }}" title="Eliminar">
                                                    <i class="fa-solid fa-trash" style="font-size: 0.7rem;"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            No se encontraron parroquias registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Paginación --}}
                    @if($parroquias->hasPages())
                        <div class="mt-3 px-3 d-flex justify-content-end">
                            {{ $parroquias->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
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

                // 2. Filtros (Enter y Change)
                const searchInput = document.getElementById('searchInput');
                const cantonFilter = document.getElementById('cantonFilter');

                function applyFilters() {
                    const searchValue = encodeURIComponent(searchInput.value);
                    const cantonValue = encodeURIComponent(cantonFilter.value);
                    window.location.href = "{{ route('parroquias.index') }}?search=" + searchValue + "&canton_id=" + cantonValue;
                }

                if (searchInput) {
                    searchInput.addEventListener('keypress', function (e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            applyFilters();
                        }
                    });
                }

                if (cantonFilter) {
                    cantonFilter.addEventListener('change', applyFilters);
                }

                // 3. Modal
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modalEl.querySelector('.modal-content').innerHTML = `
                            <div class="p-5 text-center">
                                <div class="spinner-border text-primary"></div>
                                <p class="mt-2 text-secondary">Cargando...</p>
                            </div>`;
                        modal.show();
                        fetch(this.getAttribute('data-url'))
                            .then(r => r.text())
                            .then(h => { modalEl.querySelector('.modal-content').innerHTML = h; });
                    });
                });

                // 4. Delete
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function () {
                        Swal.fire({
                            title: '¿Eliminar Cantón?',
                            html: `¿Deseas eliminar el cantón <b>"${this.getAttribute('data-item')}"</b>?`,
                            icon: 'warning',
                            showCancelButton: true,

                            // COLOR DEL BOTÓN CONFIRMAR (Rojo para peligro)
                            confirmButtonColor: '#d33',

                            // COLOR DEL BOTÓN CANCELAR (Aquí pones el Azul)
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

                function toggleSelectAll() {
                    const c = document.getElementById('selectAll').checked;
                    document.querySelectorAll('.check-item').forEach(x => x.checked = c);
                }
            });
        </script>
    </main>
</x-app-layout>