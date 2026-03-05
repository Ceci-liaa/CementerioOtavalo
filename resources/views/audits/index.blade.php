<x-app-layout>
    <style>
        .table thead th {
            font-size: 13px !important;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            font-weight: 700 !important;
            padding-top: 15px !important;
            padding-bottom: 15px !important;
        }
        .badge { font-weight: 600; text-transform: uppercase; font-size: 0.7rem; }
        pre {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 8px;
            max-height: 120px;
            overflow-y: auto;
            font-size: 0.75rem;
            text-align: left;
            margin: 0;
            white-space: pre-wrap;
        }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }

        /* BOTONES PERSONALIZADOS */
        .btn-purple { background-color: #6f42c1 !important; color: white !important; border: none; }
        .btn-purple:hover { background-color: #59359a !important; }
        .btn-grey { background-color: #6c757d !important; color: white !important; border: none; }
        .btn-grey:hover { background-color: #5a6268 !important; }

        /* BADGES OSCUROS */
        .badge-created { background-color: #1b5e20 !important; color: white !important; } /* Verde oscuro */
        .badge-updated { background-color: #01579b !important; color: white !important; } /* Azul oscuro */
        .badge-deleted { background-color: #b71c1c !important; color: white !important; } /* Rojo oscuro */
        .badge-default { background-color: #424242 !important; color: white !important; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="font-weight-bolder mb-1" style="color: #1c2a48;">Historial de Auditoría</h3>
                    <p class="mb-0 text-secondary text-sm">Registro detallado de todas las actividades y cambios en el sistema.</p>
                </div>
            </div>

            <div class="card shadow-sm border mb-4">
                <div class="card-body p-3">
                    <!-- Filtros y Búsqueda -->
                    <form method="GET" action="{{ route('auditoria.index') }}" class="row g-2 align-items-end" id="auditFilterForm">
                        <!-- Búsqueda General -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-secondary mb-1">🔍 Buscar:</label>
                            <div class="input-group input-group-sm bg-white border rounded shadow-none">
                                <span class="input-group-text bg-white border-0 pe-1 text-secondary">
                                    <i class="fas fa-search" id="searchIcon" style="font-size: 0.8rem;"></i>
                                    <i class="fas fa-spinner fa-spin text-primary" id="searchSpinner" style="display:none; font-size: 0.8rem;"></i>
                                </span>
                                <input type="text" name="search" id="searchInput" class="form-control border-0 ps-1 shadow-none" 
                                       placeholder="Usuario, módulo..." value="{{ request('search') }}" style="font-size: 0.85rem;">
                            </div>
                        </div>

                        <!-- Fecha Específica -->
                        <div class="col-md-2">
                            <label for="fechaId" class="form-label fw-bold small text-secondary mb-1">📅 Fecha:</label>
                            <input type="date" name="fecha" id="fechaId" value="{{ request('fecha') }}" class="form-control form-control-sm filter-input shadow-none">
                        </div>

                        <!-- Rango Desde -->
                        <div class="col-md-2">
                            <label for="fechaInicioId" class="form-label fw-bold small text-secondary mb-1">📆 Desde:</label>
                            <input type="date" name="fecha_inicio" id="fechaInicioId" value="{{ request('fecha_inicio') }}" class="form-control form-control-sm filter-input shadow-none">
                        </div>

                        <!-- Rango Hasta -->
                        <div class="col-md-2">
                            <label for="fechaFinId" class="form-label fw-bold small text-secondary mb-1">📆 Hasta:</label>
                            <input type="date" name="fecha_fin" id="fechaFinId" value="{{ request('fecha_fin') }}" class="form-control form-control-sm filter-input shadow-none">
                        </div>

                        <!-- Botones de Acción -->
                        <div class="col-md-3">
                            <div class="d-flex gap-2 h-100">
                                <button type="submit" class="btn btn-purple btn-sm mb-0 flex-grow-1 shadow-sm d-flex align-items-center justify-content-center" style="font-weight: 600; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; height: 31px;">
                                    <i class="fas fa-filter me-1"></i> Filtrar
                                </button>
                                <a href="{{ route('auditoria.index') }}" class="btn btn-grey btn-sm mb-0 flex-grow-1 shadow-sm d-flex align-items-center justify-content-center" title="Limpiar Filtros" style="font-weight: 600; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.5px; height: 31px;">
                                    <i class="fas fa-eraser me-1"></i> Limpiar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border">
                <div class="card-body p-0 pb-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="opacity-10" style="width: 60px;">ID</th>
                                    <th class="opacity-10">Módulo</th>
                                    <th class="opacity-10">ID Reg.</th>
                                    <th class="opacity-10">Evento</th>
                                    <th class="opacity-10 text-start ps-4">Usuario</th>
                                    <th class="opacity-10" style="width: 25%;">Antes</th>
                                    <th class="opacity-10" style="width: 25%;">Después</th>
                                    <th class="opacity-10">Fecha / Hora</th>
                                </tr>
                            </thead>
                            <tbody id="auditTbody">
                                @forelse ($audits as $audit)
                                    <tr>
                                        <td class="text-xs fw-bold text-secondary">{{ $audit->id }}</td>
                                        <td class="fw-bold text-dark text-sm">
                                            {{ class_basename($audit->auditable_type) }}
                                        </td>
                                        <td>
                                            <span class="badge border bg-light text-dark">{{ $audit->auditable_id }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match(strtolower($audit->event)) {
                                                    'created' => 'badge-created',
                                                    'updated' => 'badge-updated',
                                                    'deleted' => 'badge-deleted',
                                                    default => 'badge-default'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $audit->event }}</span>
                                        </td>
                                        <td class="text-start ps-4">
                                            <div class="d-flex flex-column">
                                                <span class="text-sm fw-bold text-dark">{{ optional($audit->user)->name ?? 'Sistema' }}</span>
                                                <span class="text-xs text-secondary">{{ optional($audit->user)->email ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            @if(!empty($audit->old_values))
                                                <pre>{{ json_encode($audit->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                <span class="text-muted text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            @if(!empty($audit->new_values))
                                                <pre>{{ json_encode($audit->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                <span class="text-muted text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="text-sm fw-bold">
                                            {{ $audit->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            No se encontraron registros de auditoría.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 px-3 d-flex justify-content-center" id="auditPagination">
                        {{ $audits->links() }}
                    </div>
                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const searchInput = document.getElementById('searchInput');
            const fechaInput = document.getElementById('fechaId');
            const fechaInicioInput = document.getElementById('fechaInicioId');
            const fechaFinInput = document.getElementById('fechaFinId');
            const filterForm = document.getElementById('auditFilterForm');
            
            let searchTimeout = null;
            let currentAbort = null;
            let requestId = 0;

            function showSpinner() {
                const icon = document.getElementById('searchIcon');
                const spinner = document.getElementById('searchSpinner');
                if(icon) icon.style.display = 'none';
                if(spinner) spinner.style.display = 'inline-block';
            }

            function hideSpinner() {
                const icon = document.getElementById('searchIcon');
                const spinner = document.getElementById('searchSpinner');
                if(icon) icon.style.display = 'inline-block';
                if(spinner) spinner.style.display = 'none';
            }

            function performSearch() {
                if (currentAbort) currentAbort.abort();
                currentAbort = new AbortController();

                const myId = ++requestId;
                showSpinner();

                const params = new URLSearchParams();
                if (searchInput.value.trim()) params.set('search', searchInput.value.trim());
                if (fechaInput.value) params.set('fecha', fechaInput.value);
                if (fechaInicioInput.value) params.set('fecha_inicio', fechaInicioInput.value);
                if (fechaFinInput.value) params.set('fecha_fin', fechaFinInput.value);

                const url = "{{ route('auditoria.index') }}?" + params.toString();
                window.history.replaceState({}, '', url);

                fetch(url, { 
                    signal: currentAbort.signal,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(r => r.text())
                .then(html => {
                    if (myId !== requestId) return;
                    const doc = new DOMParser().parseFromString(html, 'text/html');

                    const newBody = doc.getElementById('auditTbody');
                    const currentBody = document.getElementById('auditTbody');
                    if (newBody && currentBody) currentBody.innerHTML = newBody.innerHTML;

                    const newPag = doc.getElementById('auditPagination');
                    const currentPag = document.getElementById('auditPagination');
                    if (newPag && currentPag) currentPag.innerHTML = newPag.innerHTML;
                    
                    hideSpinner();
                })
                .catch(e => {
                    if (e.name !== 'AbortError') {
                        console.error('Error en búsqueda:', e);
                        hideSpinner();
                    }
                });
            }

            function triggerSearch() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, 350);
            }

            if (filterForm) {
                filterForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    performSearch();
                });
            }

            searchInput.addEventListener('input', triggerSearch);
            fechaInput.addEventListener('change', performSearch);
            fechaInicioInput.addEventListener('change', performSearch);
            fechaFinInput.addEventListener('change', performSearch);
        });
    </script>
</x-app-layout>
