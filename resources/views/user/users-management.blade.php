<x-app-layout>
<style>
        /* 1. CORRECCIÓN DE COLOR EXACTO (VERDE PASTEL) + LETRA FINA */
        .alert-success {
            background-color: #e4f4db !important; /* El verde suave de tu imagen */
            color: #708736 !important;           /* Texto verde oscuro */
            border-color: #e4f4db !important;    /* Borde verde suave */
            
            /* CAMBIOS AQUÍ: */
            font-weight: 400 !important;         /* 400 es letra Normal (Sin negrilla) */
            font-size: 14px !important;          /* Letra un poco más pequeña */
        }
        
        /* Corrección para que la X de cerrar se vea oscura (no blanca) */
        .alert-success .btn-close {
            filter: none !important; 
            opacity: 0.5;
            color: #708736; /* Le puse el mismo color del texto para que combine */
        }
        .alert-success .btn-close:hover {
            opacity: 1;
        }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            <div class="mb-4">
                <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Administración de Usuarios</h3>
                <p class="mb-0 text-secondary text-sm">Aquí puedes gestionar los reportes de usuarios.</p>
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

            <form action="{{ route('users.generateReports') }}" method="POST" id="reportForm">
                @csrf

                <div class="d-flex justify-content-between align-items-center mb-4">
                    
                    <div class="dropdown">
                        <button class="btn text-white dropdown-toggle mb-0 px-4" 
                                style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" 
                                type="button" id="dropdownGenerate" data-bs-toggle="dropdown" aria-expanded="false">
                            Generar Reporte
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownGenerate">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    <div class="position-relative">
                        <div class="input-group bg-white border rounded overflow-hidden">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 ps-2 shadow-none" 
                                   placeholder="Buscar usuario..." id="searchInput" 
                                   value="{{ request('search') }}" style="min-width: 250px;">
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border">
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle text-center mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 50px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;"></th>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Ubicación</th>
                                        <th>Roles</th>
                                        <th>Estado</th>
                                        <th style="width:120px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td><input type="checkbox" name="users[]" value="{{ $user->id }}" style="cursor: pointer;"></td>
                                            <td class="fw-bold">{{ $user->codigo_usuario }}</td>
                                            <td class="text-start ps-3">{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone ?? 'N/A' }}</td>
                                            <td>{{ $user->location ?? 'N/A' }}</td>

                                            <td>
                                                <span class="badge border" style="background-color: #e9ecef; color: #343a40; font-size: 0.85rem; font-weight: 600;">
                                                    {{ $user->getRoleNames()->first() }}
                                                </span>
                                            </td>

                                            <td>
                                                @if($user->status)
                                                    <span class="badge" style="background-color: #19cf2bff; color: white; font-size: 0.85rem;">Activo</span>
                                                @else
                                                    <span class="badge" style="background-color: #ef1b30ff; color: white; font-size: 0.85rem;">Inactivo</span>
                                                @endif
                                            </td>
                                            
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning mb-0" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editUserModal"
                                                        data-id="{{ $user->id }}"
                                                        data-name="{{ $user->name }}"
                                                        data-email="{{ $user->email }}"
                                                        data-phone="{{ $user->phone }}"
                                                        data-location="{{ $user->location }}"
                                                        data-role="{{ $user->roles->first()->id ?? '' }}"
                                                        data-status="{{ $user->status ? 1 : 0 }}" 
                                                        title="Editar">
                                                    <i class="fa-solid fa-pen-to-square" style="font-size:.9rem;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center py-4 text-muted">No se encontraron usuarios.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title text-white" id="editUserModalLabel">Editar Usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <form id="editUserForm" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nombre</label>
                                    <input type="text" name="name" id="modalName" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" name="email" id="modalEmail" class="form-control" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Teléfono</label>
                                    <input type="text" name="phone" id="modalPhone" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Ubicación</label>
                                    <input type="text" name="location" id="modalLocation" class="form-control">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Rol</label>
                                    <select name="role_id" id="modalRole" class="form-control" required>
                                        @foreach ($roles as $roleId => $roleName)
                                            <option value="{{ $roleId }}">{{ ucfirst($roleName) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Estado</label>
                                    <select name="status" id="modalStatus" class="form-control" required>
                                        <option value="1">Activo</option>
                                        <option value="0">Inactivo</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <x-app.footer />

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                
                // 1. Ocultar alertas automáticamente
                setTimeout(function () {
                    document.querySelectorAll('.alert-temporal').forEach(alert => {
                        alert.style.transition = "opacity 0.5s";
                        alert.style.opacity = 0;
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 3000);

                // 2. Buscador
                const searchInput = document.getElementById('searchInput');
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault(); 
                        const searchTerm = this.value;
                        window.location.href = "{{ route('users-management') }}?search=" + encodeURIComponent(searchTerm);
                    }
                });

                // 3. Lógica del Modal
                var editUserModal = document.getElementById('editUserModal');
                editUserModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget;
                    
                    var id = button.getAttribute('data-id');
                    var name = button.getAttribute('data-name');
                    var email = button.getAttribute('data-email');
                    var phone = button.getAttribute('data-phone');
                    var location = button.getAttribute('data-location');
                    var role = button.getAttribute('data-role');
                    var status = button.getAttribute('data-status');

                    // Actualizar ruta del form
                    var actionUrl = "{{ route('users.update', 'ID_PLACEHOLDER') }}";
                    document.getElementById('editUserForm').action = actionUrl.replace('ID_PLACEHOLDER', id);

                    // Rellenar campos
                    document.getElementById('modalName').value = name;
                    document.getElementById('modalEmail').value = email;
                    document.getElementById('modalPhone').value = (phone && phone !== 'N/A') ? phone : '';
                    document.getElementById('modalLocation').value = (location && location !== 'N/A') ? location : '';
                    document.getElementById('modalRole').value = role;
                    document.getElementById('modalStatus').value = status;
                });
            });

            function toggleSelectAll() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('input[name="users[]"]');
                checkboxes.forEach((checkbox) => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            }
        </script>
    </main>
</x-app-layout>