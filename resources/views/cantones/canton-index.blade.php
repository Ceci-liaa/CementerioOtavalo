<x-app-layout>
    {{-- 1. ESTILOS PERSONALIZADOS --}}
    <style>
        /* Alerta Verde Pastel */
        .alert-success {
            background-color: #e4f4db !important;
            color: #708736 !important;
            border-color: #e4f4db !important;
            font-weight: 400 !important;
            font-size: 14px !important;
        }
        .alert-success .btn-close {
            filter: none !important;
            opacity: 0.5;
            color: #708736;
        }
        .alert-success .btn-close:hover { opacity: 1; }
        
        /* Alerta Roja */
        .alert-danger {
            background-color: #fde1e1 !important;
            color: #cf304a !important;
            border-color: #fde1e1 !important;
            font-weight: 400 !important;
            font-size: 14px !important;
        }
        .alert-danger .btn-close {
            filter: none !important;
            opacity: 0.5;
            color: #cf304a;
        }

        /* Badges de permisos/códigos */
        .code-badge {
            font-size: 0.85rem;
            font-weight: 600;
            background-color: #f0f2f5;
            color: #344767;
            border: 1px solid #dee2e6;
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-block;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO CON CONTADOR TOTAL --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Cantones</h3>
                        {{-- CONTADOR TOTAL DE REGISTROS --}}
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $cantones->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administra el catálogo geográfico de cantones.</p>
                </div>
                
                {{-- BOTÓN NUEVO CANTÓN --}}
                <button type="button" class="btn btn-success mb-0 px-4 shadow-sm" style="font-weight: 600;" 
                        data-bs-toggle="modal" data-bs-target="#createCantonModal">
                    <i class="fas fa-plus me-2"></i> Nuevo Cantón
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

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show alert-temporal mb-3" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- 4. BUSCADOR --}}
            <div class="row mb-4 justify-content-end">
                <div class="col-md-4">
                    <form method="GET" action="{{ route('cantones.index') }}">
                        <div class="input-group bg-white border rounded overflow-hidden shadow-sm">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" 
                                   class="form-control border-0 ps-2 shadow-none" 
                                   placeholder="Buscar por código o nombre...">
                        </div>
                    </form>
                </div>
            </div>

            {{-- 5. TABLA DE CANTONES --}}
            <div class="card shadow-sm border">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle text-center mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 50px;">
                                        <input type="checkbox" id="selectAll" style="cursor: pointer;">
                                    </th>
                                    {{-- COLUMNA NUMERACIÓN VISUAL (#) --}}
                                    <th style="width: 50px;">#</th>
                                    
                                    <th style="width: 20%;">Código</th>
                                    <th class="text-start ps-4">Nombre del Cantón</th>
                                    <th style="width: 150px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cantones as $canton)
                                    <tr>
                                        {{-- Checkbox --}}
                                        <td>
                                            <input type="checkbox" name="cantones[]" value="{{ $canton->id }}" style="cursor: pointer;">
                                        </td>

                                        {{-- NUMERACIÓN VISUAL (1, 2, 3...) --}}
                                        {{-- firstItem() obtiene el número del primer elemento de la página actual (ej: 11 en pág 2) --}}
                                        <td class="fw-bold text-secondary">
                                            {{ $cantones->firstItem() + $loop->index }}
                                        </td>

                                        {{-- CÓDIGO REAL (CA001) --}}
                                        <td>
                                            <span class="code-badge">
                                                {{ $canton->codigo ?? 'N/A' }}
                                            </span>
                                        </td>

                                        {{-- NOMBRE --}}
                                        <td class="fw-bold text-start ps-4" style="color: #344767;">
                                            {{ $canton->nombre }}
                                        </td>

                                        {{-- ACCIONES --}}
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                {{-- BOTÓN EDITAR --}}
                                                <button type="button" class="btn btn-sm btn-warning mb-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editCantonModal"
                                                        data-id="{{ $canton->id }}"
                                                        data-nombre="{{ $canton->nombre }}"
                                                        data-codigo="{{ $canton->codigo }}"
                                                        title="Editar">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size:.9rem;"></i>
                                                </button>

                                                {{-- BOTÓN ELIMINAR --}}
                                                <form action="{{ route('cantones.destroy', $canton) }}" method="POST" class="d-inline js-delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger mb-0 js-delete" 
                                                            data-item="{{ $canton->nombre }} ({{ $canton->codigo }})" title="Eliminar">
                                                        <i class="fa-solid fa-trash" style="font-size:.9rem;"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                                                <p class="mb-0">No hay cantones registrados.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Paginación --}}
                    @if($cantones->hasPages())
                        <div class="mt-3 d-flex justify-content-end">
                            {{ $cantones->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ================= VENTANAS FLOTANTES (MODALES) ================= --}}

        {{-- MODAL 1: CREAR --}}
        <div class="modal fade" id="createCantonModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title text-white fw-bold">
                            <i class="fas fa-plus-circle me-2"></i> Nuevo Cantón
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('cantones.store') }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Nombre del Cantón</label>
                                <input type="text" name="nombre" class="form-control form-control-lg" placeholder="Ej: Quito" required>
                            </div>
                            <div class="alert alert-light border d-flex align-items-center mb-0 p-3">
                                <i class="fas fa-info-circle text-primary fs-4 me-3"></i>
                                <div>
                                    <small class="text-dark fw-bold d-block">Código Automático</small>
                                    <small class="text-muted">El sistema generará el código (ej: <span class="badge bg-dark">CA001</span>) al guardar.</small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success px-4">Guardar Cantón</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL 2: EDITAR --}}
        <div class="modal fade" id="editCantonModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-pen-to-square me-2"></i> Editar Cantón
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="editCantonForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Código</label>
                                <input type="text" id="editCodigo" class="form-control bg-light text-muted fw-bold" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary">Nombre del Cantón</label>
                                <input type="text" name="nombre" id="editNombre" class="form-control form-control-lg" required>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning px-4">Actualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-app.footer />

        {{-- ================= SCRIPTS ================= --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                
                // 1. Auto-cerrar alertas
                setTimeout(function () {
                    document.querySelectorAll('.alert-temporal').forEach(alert => {
                        alert.style.transition = "opacity 0.5s";
                        alert.style.opacity = 0;
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 4000);

                // 2. Modal Editar
                var editCantonModal = document.getElementById('editCantonModal');
                editCantonModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    var id = button.getAttribute('data-id');
                    var nombre = button.getAttribute('data-nombre');
                    var codigo = button.getAttribute('data-codigo');
                    
                    document.getElementById('editNombre').value = nombre;
                    document.getElementById('editCodigo').value = codigo ? codigo : '---';
                    
                    var form = document.getElementById('editCantonForm');
                    var actionUrl = "{{ route('cantones.update', ':id') }}";
                    form.action = actionUrl.replace(':id', id);
                });

                // 3. SweetAlert Eliminar
                document.addEventListener('click', function (e) {
                    const btn = e.target.closest('.js-delete');
                    if (!btn) return;
                    const form = btn.closest('form');
                    const item = btn.getAttribute('data-item') || 'este registro';

                    Swal.fire({
                        title: '¿Eliminar Cantón?',
                        html: `¿Deseas eliminar <b>"${item}"</b>?<br>Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) form.submit();
                    });
                });

                // 4. Select All
                const selectAll = document.getElementById('selectAll');
                if(selectAll) {
                    selectAll.addEventListener('change', function() {
                        document.querySelectorAll('input[name="cantones[]"]').forEach(cb => cb.checked = this.checked);
                    });
                }
            });
        </script>
    </main>
</x-app-layout>