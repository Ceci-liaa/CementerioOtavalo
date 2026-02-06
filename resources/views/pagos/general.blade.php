<x-app-layout>
    {{-- 1. ESTILOS (Idénticos al Index de Socios + Estilos de Tabla Cantones) --}}
    <style>
        /* INPUTS */
        .input-group-text {
            border-color: #dee2e6;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #5ea6f7;
            box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25);
        }

        /* BADGES (Estilo Píldora con Sombra) */
        .badge-pill-custom {
            border-radius: 50rem;
            padding: 0.5em 1em;
            font-weight: 700;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        /* ESTILOS DE TABLA (Agregados para igualar a la vista de Cantones) */
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
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <div>
                    {{-- Mismo color de título #1c2a48 --}}
                    <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Historial de Recaudación</h3>
                    <p class="text-secondary text-sm mb-0">Listado completo de pagos registrados.</p>
                </div>

                <div class="d-flex gap-3 align-items-center mt-3 mt-md-0">
                    {{-- Tarjeta de Total --}}
                    <div class="bg-white border rounded px-3 py-2 shadow-sm d-flex align-items-center gap-3">
                        <small class="text-secondary fw-bold text-uppercase" style="font-size: 0.7rem;">Total
                            Histórico</small>
                        <h4 class="mb-0 fw-bolder text-success">${{ number_format($totalRecaudado, 2) }}</h4>
                    </div>

                    {{-- BOTÓN REGISTRAR PAGO --}}
                    <button type="button" class="btn btn-success px-4 py-2 shadow-sm mb-0 open-modal"
                        style="height: fit-content;" data-url="{{ route('pagos.create') }}">
                        <i class="fas fa-plus me-2"></i> Registrar Pago
                    </button>
                </div>
            </div>

            {{-- 3. BUSCADOR --}}
            <div class="d-flex justify-content-end mb-4">
                <form method="GET">
                    <div class="input-group input-group-sm" style="width: 260px;">
                        <input type="text" name="search" class="form-control shadow-none" placeholder="Buscar..."
                            value="{{ request('search') }}">

                        <button class="btn btn-primary mb-0 fw-bold" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- 4. TABLA (Estilo Actualizado a Cantones) --}}
            <div class="card shadow-sm border">
                {{-- Cambié p-3 a p-0 pb-2 para que la tabla toque los bordes como en Cantones --}}
                <div class="card-body p-0 pb-2">
                    <div class="table-responsive">
                        {{-- Quité table-bordered para limpieza visual, agregué estilos de cabecera --}}
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="opacity-10" style="width: 80px;"># Recibo</th>
                                    <th class="opacity-10 text-start ps-4">Socio</th>
                                    <th class="opacity-10">Años Cancelados</th>
                                    <th class="opacity-10">Fecha</th>
                                    <th class="opacity-10">Total</th>
                                    <th class="opacity-10" style="width:180px;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recibos as $recibo)
                                    <tr>
                                        <td class="text-sm fw-bold text-secondary">{{ $recibo->id }}</td>

                                        <td class="text-start ps-4">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark text-sm">
                                                    {{ $recibo->socio->apellidos }} {{ $recibo->socio->nombres }}
                                                </span>
                                                <span class="text-xs text-secondary">{{ $recibo->socio->cedula }}</span>
                                            </div>
                                        </td>

                                        {{-- Años --}}
                                        <td>
                                            <span class="badge badge-pill-custom"
                                                style="background-color: #0d6efd; color: white;">
                                                {{ $recibo->anios_desc }}
                                            </span>
                                        </td>

                                        <td class="text-secondary text-sm fw-bold">
                                            {{ $recibo->fecha_pago->format('d/m/Y') }}
                                        </td>

                                        <td>
                                            <span class="fs-6 fw-bold text-dark">${{ number_format($recibo->total, 2) }}</span>
                                        </td>

                                        <td>
                                            {{-- Botones de acción --}}
                                            <div class="d-flex justify-content-center align-items-center">
                                                <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal"
                                                    data-url="{{ route('pagos.historial_socio', $recibo->socio_id) }}" 
                                                    title="Ver Historial Completo de este Socio">
                                                    <i class="fas fa-eye text-white" style="font-size: 0.7rem;"></i>
                                                </button>

                                                <button type="button" class="btn btn-sm btn-warning mb-0 btn-action open-modal"
                                                    data-url="{{ route('pagos.edit', $recibo->id) }}" title="Corregir">
                                                    <i class="fas fa-pen" style="font-size: 0.7rem;"></i>
                                                </button>

                                                </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-5 text-center text-muted">No se encontraron recibos
                                            registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- Paginación --}}
                <div class="card-footer py-3 d-flex justify-content-end">
                    {{ $recibos->links() }}
                </div>
            </div>
        </div>

        {{-- MODAL CONTAINER --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    {{-- Aquí carga el AJAX --}}
                </div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS (Tu lógica intacta) --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);

                document.body.addEventListener('click', function (e) {
                    const btn = e.target.closest('.open-modal');
                    if (btn) {
                        e.preventDefault();

                        // Spinner estilo Bootstrap
                        modalEl.querySelector('.modal-content').innerHTML = `
                            <div class="p-5 text-center">
                                <div class="spinner-border text-primary"></div>
                                <div class="mt-2 text-muted small fw-bold">Cargando información...</div>
                            </div>`;

                        modal.show();

                        fetch(btn.getAttribute('data-url'))
                            .then(r => r.text())
                            .then(html => {
                                modalEl.querySelector('.modal-content').innerHTML = html;
                                // Reactivar scripts internos
                                modalEl.querySelectorAll("script").forEach(oldScript => {
                                    const newScript = document.createElement("script");
                                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                                    oldScript.parentNode.replaceChild(newScript, oldScript);
                                });
                            })
                            .catch(err => {
                                console.error(err);
                                modalEl.querySelector('.modal-content').innerHTML =
                                    '<div class="p-4 text-danger text-center fw-bold"><i class="fas fa-exclamation-circle me-2"></i>Error al cargar contenido.</div>';
                            });
                    }
                });
            });
        </script>
    </main>
</x-app-layout>