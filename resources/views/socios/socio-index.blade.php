<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        .alert-success {
            background-color: #e4f4db !important;
            color: #708736 !important;
            border: 1px solid #cbebc4 !important;
            border-radius: 6px;
        }
        .alert-danger {
            background-color: #ffe6e6 !important;
            color: #d63031 !important;
            border: 1px solid #fadbd8 !important;
            border-radius: 6px;
        }
        .btn-reporte { background-color: #5ea6f7; border-color: #5ea6f7; color: white; font-weight: 600; }
        .btn-reporte:hover { background-color: #4b93e6; color: white; }
        .bg-light-badge { background-color: #f8f9fa; color: #344767; border: 1px solid #dee2e6; }
        
        .input-group-text { background-color: white; border-right: none; }
        .input-search { border-left: none; }
        .input-search:focus { box-shadow: none; border-color: #ced4da; }
        .input-group:focus-within .form-control, .input-group:focus-within .input-group-text { border-color: #5ea6f7; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">

            {{-- ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Socios</h3>
                        <span class="badge bg-light-badge rounded-pill px-3 py-2" style="align-self: center;">
                            Total: {{ $socios->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm mt-1">Aquí puedes gestionar y generar reportes de los socios.</p>
                </div>

                <button type="button" class="btn btn-success px-4 open-modal" data-url="{{ route('socios.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Socio
                </button>
            </div>

            {{-- ALERTAS --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3 fs-6 alert-auto-dismiss">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-3 fs-6 alert-auto-dismiss">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- FORMULARIO PRINCIPAL --}}
            <form action="{{ route('socios.reports') }}" method="POST" id="reportForm">
                @csrf
                <input type="hidden" name="comunidad_id" value="{{ request('comunidad_id') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 gap-3">
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn btn-reporte dropdown-toggle mb-0 px-4 w-100 w-md-auto" type="button" data-bs-toggle="dropdown">
                            Generar Reporte
                        </button>
                        <ul class="dropdown-menu shadow border-0 mt-2">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item py-2"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item py-2"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2 w-100 w-md-auto">
                        <select id="comunidadFilter" class="form-select" style="min-width: 160px;">
                            <option value="">Todas las Comunidades</option>
                            @foreach($comunidades as $c)
                                <option value="{{ $c->id }}" @selected(request('comunidad_id') == $c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                        <div class="input-group" style="min-width: 200px;">
                            <span class="input-group-text text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control input-search" placeholder="Buscar..." id="searchInput" value="{{ request('search') }}">
                        </div>
                    </div>
                </div>

                {{-- TABLA --}}
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        <th style="width: 40px;"><input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer; transform: scale(1.2);"></th>
                                        <th class="text-white">Código</th>
                                        <th class="text-white">Cédula</th>
                                        <th class="text-white">Nombre Completo</th>
                                        <th class="text-white">Comunidad</th>
                                        <th class="text-white">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($socios as $s)
                                        <tr>
                                            <td><input type="checkbox" name="ids[]" value="{{ $s->id }}" class="check-item" style="cursor: pointer; transform: scale(1.2);"></td>
                                            <td class="fw-bold text-dark">{{ $s->codigo }}</td>
                                            <td>{{ $s->cedula }}</td>
                                            <td class="text-start ps-4">{{ $s->apellidos }} {{ $s->nombres }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $s->comunidad?->nombre ?? 'N/A' }}</span></td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm px-3 mb-0 open-modal" data-url="{{ route('socios.show', $s) }}">
                                                    <i class="fa fa-eye text-white"></i>
                                                </button>
                                                <button type="button" class="btn btn-warning btn-sm px-3 mb-0 open-modal" data-url="{{ route('socios.edit', $s) }}">
                                                    <i class="fa-solid fa-pen text-white"></i>
                                                </button>
                                                
                                                {{-- BOTÓN ELIMINAR --}}
                                                {{-- La clase 'js-delete-btn' será detectada por SweetAlert abajo --}}
                                                <button type="button" class="btn btn-danger btn-sm px-3 mb-0 js-delete-btn"
                                                        data-url="{{ route('socios.destroy', $s) }}" 
                                                        data-item="{{ $s->apellidos }} {{ $s->nombres }}">
                                                    <i class="fa-solid fa-trash text-white"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron socios registrados.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3 d-flex justify-content-end border-top">{{ $socios->links() }}</div>
                    </div>
                </div>
            </form>

            {{-- Formulario oculto para eliminar --}}
            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO (AJAX) --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content"></div>
            </div>
        </div>

        <x-app.footer />

        {{-- IMPORTANTE: Librería SweetAlert2 (Por si no la tienes global) --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        {{-- SCRIPTS --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                
                // 1. Alertas Auto-Desvanecer (5 seg)
                setTimeout(function() {
                    document.querySelectorAll('.alert-auto-dismiss').forEach(alert => {
                        alert.classList.remove('show');
                        alert.classList.add('fade');
                        setTimeout(() => alert.remove(), 500); 
                    });
                }, 5000);

                // 2. Modal AJAX (Create/Edit)
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const url = this.getAttribute('data-url');
                        const contentDiv = modalEl.querySelector('.modal-content');
                        contentDiv.innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(url).then(res => res.text()).then(html => { contentDiv.innerHTML = html; })
                            .catch(err => { contentDiv.innerHTML = '<div class="p-4 text-danger">Error al cargar.</div>'; });
                    });
                });

                // 3. SWEETALERT PARA ELIMINAR (IGUAL A TU EJEMPLO DE CANTONES)
                document.addEventListener('click', function (e) {
                    // Detectamos clic en cualquier botón con clase .js-delete-btn
                    const btn = e.target.closest('.js-delete-btn');
                    if (!btn) return;

                    const url = btn.getAttribute('data-url');
                    const item = btn.getAttribute('data-item') || 'este registro';

                    // Lanzamos SweetAlert
                    Swal.fire({
                        title: '¿Eliminar Socio?',
                        html: `¿Deseas eliminar a <b>"${item}"</b>?<br>Esta acción no se puede deshacer.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Si confirma, usamos el formulario oculto para enviar la petición DELETE
                            const f = document.getElementById('deleteForm');
                            f.action = url;
                            f.submit();
                        }
                    });
                });

                // 4. Filtros
                const search = document.getElementById('searchInput');
                const filter = document.getElementById('comunidadFilter');
                function apply() { window.location.href = "{{ route('socios.index') }}?search=" + encodeURIComponent(search.value) + "&comunidad_id=" + filter.value; }
                if(filter) filter.addEventListener('change', apply);
                if(search) search.addEventListener('keypress', e => { if(e.key === 'Enter') { e.preventDefault(); apply(); } });
            });

            function toggleSelectAll() {
                let c = document.getElementById('selectAll').checked;
                document.querySelectorAll('.check-item').forEach(x => x.checked = c);
            }
        </script>
    </main>
</x-app-layout>