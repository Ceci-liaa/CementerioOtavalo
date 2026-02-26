<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        /* INPUTS */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 250px; }

        /* ALERTAS */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; }
        .alert-info { background-color: #e3f2fd !important; color: #0c5460 !important; border-color: #b8daff !important; }

        /* Badges de estado (Estilo Píldora con Sombra) */
        .badge-pill-custom {
            border-radius: 50rem;
            padding: 0.5em 1em;
            font-weight: 700;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        .badge-pendiente { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-emitida { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .badge-anulada { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* ESTILOS DE TABLA (Estilo Historial de Pagos) */
        .table thead th {
            font-size: 14px !important;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            font-weight: 700 !important;
            padding-top: 15px !important;
            padding-bottom: 15px !important;
        }
        .btn-action { margin-right: 4px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Facturas</h3>
                        <span class="badge bg-light text-dark border" id="facturasTotalBadge" style="font-size: 0.8rem;">
                            Total: {{ $facturas->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Control de emisiones, cobros y anulaciones.</p>
                </div>

                {{-- Botón Nueva Factura --}}
                <button type="button" class="btn btn-success px-4 open-modal" 
                        style="height: fit-content;"
                        data-url="{{ route('facturas.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nueva Factura
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

            {{-- 4. BUSCADOR EN TIEMPO REAL --}}
            <div class="d-flex justify-content-end mb-4">
                <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                    <span class="input-group-text bg-white border-0 pe-1 text-secondary" id="searchIconWrapper">
                        <i class="fas fa-search" id="searchIcon"></i>
                        <i class="fas fa-spinner fa-spin text-primary" id="searchSpinner" style="display:none;"></i>
                    </span>
                    <input type="text" class="form-control border-0 ps-1 shadow-none" placeholder="Buscar factura, cliente..."
                        id="searchInput" value="{{ $q }}">
                </div>
            </div>

            {{-- 5. TABLA (Estilo Historial de Pagos) --}}
            <div class="card shadow-sm border">
                <div class="card-body p-0 pb-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="opacity-10" style="width: 50px;">#</th>
                                    <th class="opacity-10">Código</th>
                                    <th class="opacity-10">Fecha</th>
                                    <th class="opacity-10 text-start ps-4">Cliente / Socio</th>
                                    <th class="opacity-10">Total</th>
                                    <th class="opacity-10">Estado</th>
                                    <th class="opacity-10" style="width:180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="facturasTbody">
                                @forelse ($facturas as $factura)
                                    @php
                                        $estado = strtoupper($factura->estado);
                                        $badgeClass = match($estado) {
                                            'BORRADOR' => 'badge-pendiente badge-pill-custom',
                                            'EMITIDA' => 'badge-emitida badge-pill-custom',
                                            'ANULADA' => 'badge-anulada badge-pill-custom',
                                            default => 'bg-light text-dark border badge-pill-custom'
                                        };
                                        $txtEstado = ($estado == 'BORRADOR') ? 'EN PROCESO' : $estado;
                                    @endphp
                                    <tr>
                                        <td class="text-sm fw-bold text-secondary">{{ $facturas->firstItem() + $loop->index }}</td>
                                        <td class="fw-bold text-dark">{{ $factura->codigo }}</td>
                                        <td class="text-secondary text-sm fw-bold">{{ $factura->fecha?->format('d/m/Y') }}</td>
                                        <td class="text-start ps-4">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark text-sm">{{ $factura->cliente_nombre }} {{ $factura->cliente_apellido }}</span>
                                                <span class="text-xs text-secondary">{{ $factura->cliente_cedula }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fs-6 fw-bold text-dark">${{ number_format($factura->total, 2) }}</span>
                                        </td>
                                        <td><span class="badge {{ $badgeClass }}">{{ $txtEstado }}</span></td>

                                        {{-- ACCIONES --}}
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center">
                                                @if($factura->estado === 'BORRADOR')
                                                    {{-- GENERAR PDF (EMITIR) --}}
                                                    <a href="{{ route('facturas.pdf', $factura) }}" 
                                                       class="btn btn-sm btn-success mb-0 btn-action" 
                                                       title="Generar PDF y Emitir"
                                                       onclick="return confirmarEmision(event)">
                                                        <i class="fa-solid fa-file-pdf" style="font-size: 0.7rem;"></i>
                                                    </a>
                                                    {{-- EDITAR --}}
                                                    <button type="button" class="btn btn-sm btn-warning mb-0 btn-action open-modal" 
                                                            data-url="{{ route('facturas.edit', $factura) }}" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    {{-- ANULAR (Borrador) --}}
                                                    <button type="button" class="btn btn-sm btn-outline-danger mb-0 btn-action js-anular-btn"
                                                            data-url="{{ route('facturas.anular', $factura) }}" 
                                                            data-codigo="{{ $factura->codigo }}"
                                                            title="Anular">
                                                        <i class="fa-solid fa-ban" style="font-size: 0.7rem;"></i>
                                                    </button>

                                                @elseif($factura->estado === 'EMITIDA')
                                                    {{-- PDF (Reimprimir) --}}
                                                    <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-sm btn-dark mb-0 btn-action" title="Descargar PDF">
                                                        <i class="fa-solid fa-print" style="font-size: 0.7rem;"></i>
                                                    </a>
                                                    {{-- VER --}}
                                                    <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal" 
                                                            data-url="{{ route('facturas.show', $factura) }}" title="Ver">
                                                        <i class="fa fa-eye text-white" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    {{-- ANULAR --}}
                                                    <button type="button" class="btn btn-sm btn-outline-danger mb-0 btn-action js-anular-btn"
                                                            data-url="{{ route('facturas.anular', $factura) }}" 
                                                            data-codigo="{{ $factura->codigo }}"
                                                            title="Anular">
                                                        <i class="fa-solid fa-ban" style="font-size: 0.7rem;"></i>
                                                    </button>

                                                @else 
                                                    {{-- ANULADA --}}
                                                    <button type="button" class="btn btn-sm btn-secondary mb-0" 
                                                            onclick="Swal.fire('Motivo', '{{ $factura->motivo_anulacion }}', 'info')">
                                                        <i class="fa-solid fa-info-circle" style="font-size: 0.7rem;"></i> Motivo
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="py-5 text-center text-muted">No hay facturas registradas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Paginación --}}
                <div class="card-footer py-3 d-flex justify-content-end" id="facturasPagination">
                    {{ $facturas->links() }}
                </div>
            </div>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content"></div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            // Confirmación PDF + Recarga
            function confirmarEmision(e) {
                e.preventDefault(); 
                const url = e.currentTarget.getAttribute('href');
                Swal.fire({
                    title: '¿Generar PDF?',
                    html: 'La factura pasará a estado <b>EMITIDA</b> y se descargará el archivo.<br><small class="text-muted">Esta acción no se puede deshacer.</small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, Generar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url; // Descarga directa
                        setTimeout(() => { location.reload(); }, 2000); // Recarga para ver cambio estado
                    }
                });
                return false;
            }

            document.addEventListener("DOMContentLoaded", function () {
                // Alertas
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; 
                        alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 3000);

                // ── Búsqueda en Tiempo Real (AJAX) ──
                const searchInput = document.getElementById('searchInput');
                let searchTimeout = null;
                let currentAbort = null;
                let requestId = 0;

                function showSpinner() {
                    const ic = document.getElementById('searchIcon');
                    const sp = document.getElementById('searchSpinner');
                    if (ic) ic.style.display = 'none';
                    if (sp) sp.style.display = 'inline-block';
                }

                function hideSpinner() {
                    const ic = document.getElementById('searchIcon');
                    const sp = document.getElementById('searchSpinner');
                    if (ic) ic.style.display = 'inline-block';
                    if (sp) sp.style.display = 'none';
                }

                function liveSearch() {
                    if (currentAbort) currentAbort.abort();
                    currentAbort = new AbortController();

                    const myId = ++requestId;
                    showSpinner();

                    const params = new URLSearchParams();
                    if (searchInput && searchInput.value.trim()) params.set('q', searchInput.value.trim());

                    const url = "{{ route('facturas.index') }}?" + params.toString();
                    window.history.replaceState({}, '', url);

                    fetch(url, { signal: currentAbort.signal })
                        .then(function(r) { return r.text(); })
                        .then(function(html) {
                            if (myId !== requestId) return;

                            var doc = new DOMParser().parseFromString(html, 'text/html');

                            var newTbody = doc.getElementById('facturasTbody');
                            var curTbody = document.getElementById('facturasTbody');
                            if (newTbody && curTbody) curTbody.innerHTML = newTbody.innerHTML;

                            var newPag = doc.getElementById('facturasPagination');
                            var curPag = document.getElementById('facturasPagination');
                            if (newPag && curPag) curPag.innerHTML = newPag.innerHTML;

                            var newBadge = doc.getElementById('facturasTotalBadge');
                            var curBadge = document.getElementById('facturasTotalBadge');
                            if (newBadge && curBadge) curBadge.textContent = newBadge.textContent;

                            hideSpinner();
                        })
                        .catch(function(e) {
                            if (e.name !== 'AbortError') {
                                console.error('Error en búsqueda:', e);
                                hideSpinner();
                            }
                        });
                }

                function triggerSearch() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(liveSearch, 350);
                }

                if (searchInput) {
                    searchInput.addEventListener('input', triggerSearch);
                    searchInput.addEventListener('keyup', triggerSearch);
                    searchInput.addEventListener('keypress', function(e) { if (e.key === 'Enter') e.preventDefault(); });
                }

                // Modal
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.addEventListener('click', function (e) {
                    const btn = e.target.closest('.open-modal');
                    if (btn) {
                        e.preventDefault();
                        const url = btn.getAttribute('data-url');
                        modalEl.querySelector('.modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(url).then(r => r.text()).then(h => {
                            const c = modalEl.querySelector('.modal-content');
                            c.innerHTML = h;
                            const s = c.querySelectorAll("script");
                            s.forEach(os => {
                                const ns = document.createElement("script");
                                Array.from(os.attributes).forEach(a => ns.setAttribute(a.name, a.value));
                                ns.appendChild(document.createTextNode(os.innerHTML));
                                os.parentNode.replaceChild(ns, os);
                            });
                        });
                    }
                });

                // Anular con Motivo (delegación para que funcione tras AJAX)
                document.body.addEventListener('click', function(e) {
                    const btn = e.target.closest('.js-anular-btn');
                    if (!btn) return;
                    const url = btn.getAttribute('data-url');
                    const codigo = btn.getAttribute('data-codigo');
                    Swal.fire({
                        title: `Anular ${codigo}`,
                        text: "Esta acción invalidará el documento.",
                        input: 'textarea',
                        inputLabel: 'Motivo (Obligatorio):',
                        inputPlaceholder: 'Ej: Error de digitación...',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Sí, Anular',
                        cancelButtonText: 'Cancelar',
                        inputValidator: (val) => { if (!val) return '¡Escriba un motivo!'; }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = url;
                            form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="PUT"><input type="hidden" name="motivo" value="${result.value}">`;
                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                });
            });
        </script>
    </main>
</x-app-layout>