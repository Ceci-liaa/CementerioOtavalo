<x-app-layout>
    {{-- 1. ESTILOS --}}
    <style>
        /* ESTILO ALERTAS */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; }
        .alert-info { background-color: #e3f2fd !important; color: #0c5460 !important; border-color: #b8daff !important; }
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 250px; }
        
        /* Badges de estado */
        .badge-pendiente { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-emitida { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .badge-anulada { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Facturas</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
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

            {{-- 4. FILTROS --}}
            <form method="GET" action="{{ route('facturas.index') }}" class="mb-4">
                <div class="d-flex flex-column flex-md-row justify-content-end align-items-center gap-2">
                    <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter">
                        <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                        <input type="text" name="q" class="form-control border-0 ps-1 shadow-none" 
                               placeholder="Buscar factura, cliente..." 
                               value="{{ $q }}">
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm mb-0">Buscar</button>
                    @if($q)
                        <a href="{{ route('facturas.index') }}" class="btn btn-outline-secondary btn-sm mb-0">Limpiar</a>
                    @endif
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
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Cliente / Socio</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th style="width:160px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($facturas as $factura)
                                    @php
                                        $estado = strtoupper($factura->estado);
                                        $badgeClass = match($estado) {
                                            'BORRADOR' => 'badge-pendiente',
                                            'EMITIDA' => 'badge-emitida',
                                            'ANULADA' => 'badge-anulada',
                                            default => 'bg-light text-dark border'
                                        };
                                        $txtEstado = ($estado == 'BORRADOR') ? 'EN PROCESO' : $estado;
                                    @endphp
                                    <tr>
                                        <td class="fw-bold text-secondary">{{ $facturas->firstItem() + $loop->index }}</td>
                                        <td class="fw-bold text-dark">{{ $factura->codigo }}</td>
                                        <td>{{ $factura->fecha?->format('d/m/Y') }}</td>
                                        <td class="text-start ps-4">
                                            <div class="fw-bold">{{ $factura->cliente_nombre }} {{ $factura->cliente_apellido }}</div>
                                            <small class="text-muted">{{ $factura->cliente_cedula }}</small>
                                        </td>
                                        <td class="fw-bold text-end pe-4">$ {{ number_format($factura->total, 2) }}</td>
                                        <td><span class="badge {{ $badgeClass }}">{{ $txtEstado }}</span></td>

                                        {{-- ACCIONES --}}
                                        <td>
                                            <div class="d-flex justify-content-center gap-1">
                                                @if($factura->estado === 'BORRADOR')
                                                    {{-- GENERAR PDF (EMITIR) --}}
                                                    <a href="{{ route('facturas.pdf', $factura) }}" 
                                                       class="btn btn-sm btn-success mb-0" 
                                                       title="Generar PDF y Emitir"
                                                       onclick="return confirmarEmision(event)">
                                                        <i class="fa-solid fa-file-pdf"></i>
                                                    </a>
                                                    {{-- EDITAR --}}
                                                    <button type="button" class="btn btn-sm btn-warning mb-0 open-modal" 
                                                            data-url="{{ route('facturas.edit', $factura) }}" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </button>
                                                    {{-- ANULAR (Borrador) --}}
                                                    <button type="button" class="btn btn-sm btn-outline-danger mb-0 js-anular-btn"
                                                            data-url="{{ route('facturas.anular', $factura) }}" 
                                                            data-codigo="{{ $factura->codigo }}"
                                                            title="Anular">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </button>

                                                @elseif($factura->estado === 'EMITIDA')
                                                    {{-- PDF (Reimprimir) --}}
                                                    <a href="{{ route('facturas.pdf', $factura) }}" class="btn btn-sm btn-dark mb-0" title="Descargar PDF">
                                                        <i class="fa-solid fa-print"></i>
                                                    </a>
                                                    {{-- VER --}}
                                                    <button type="button" class="btn btn-sm btn-info mb-0 open-modal" 
                                                            data-url="{{ route('facturas.show', $factura) }}" title="Ver">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                    {{-- ANULAR --}}
                                                    <button type="button" class="btn btn-sm btn-outline-danger mb-0 js-anular-btn"
                                                            data-url="{{ route('facturas.anular', $factura) }}" 
                                                            data-codigo="{{ $factura->codigo }}"
                                                            title="Anular">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </button>

                                                @else 
                                                    {{-- ANULADA --}}
                                                    <button type="button" class="btn btn-sm btn-secondary mb-0" 
                                                            onclick="Swal.fire('Motivo', '{{ $factura->motivo_anulacion }}', 'info')">
                                                        <i class="fa-solid fa-info-circle"></i> Motivo
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center py-4 text-muted">No hay facturas registradas.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-end">{{ $facturas->links() }}</div>
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
                    text: "La factura pasará a estado EMITIDA y se descargará el archivo.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
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

                // Anular con Motivo
                document.querySelectorAll('.js-anular-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const url = this.getAttribute('data-url');
                        const codigo = this.getAttribute('data-codigo');
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
            });
        </script>
    </main>
</x-app-layout>