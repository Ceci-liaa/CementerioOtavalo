<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- ENCABEZADO Y BOTONES --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Historial de Recaudación</h3>
                    <p class="text-secondary text-sm mb-0">Listado completo de pagos registrados.</p>
                </div>
                
                <div class="d-flex gap-3 align-items-center mt-3 mt-md-0">
                    {{-- Tarjeta de Total Recaudado --}}
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body p-2 px-4 text-center">
                            <small class="text-uppercase fw-bold opacity-75" style="font-size: 0.75rem;">Total Histórico</small>
                            <h4 class="mb-0 fw-bolder text-white">${{ number_format($totalRecaudado, 2) }}</h4>
                        </div>
                    </div>

                    {{-- BOTÓN REGISTRAR PAGO --}}
                    {{-- Fíjate que tiene la clase 'open-modal' y el data-url correcto --}}
                    <button type="button" class="btn btn-dark px-4 py-2 shadow-sm mb-0 open-modal" 
                            style="height: fit-content;"
                            data-url="{{ route('pagos.create') }}">
                        <i class="fas fa-plus-circle me-2"></i> Registrar Pago
                    </button>
                </div>
            </div>

            {{-- BUSCADOR --}}
            <div class="card shadow-sm border mb-4">
                <div class="card-body p-3">
                    <form method="GET">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-secondary"></i></span>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0" placeholder="Buscar en historial por socio o cédula...">
                            <button class="btn btn-dark mb-0 fw-bold" type="submit">Buscar</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="card shadow-sm border">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-light text-secondary text-uppercase text-xs fw-bolder sticky-top">
                                <tr>
                                    <th># Recibo</th>
                                    <th class="text-start ps-4">Socio</th>
                                    <th>Año Pagado</th>
                                    <th>Fecha Pago</th>
                                    <th>Monto</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pagos as $pago)
                                    <tr>
                                        <td class="text-secondary fw-bold">{{ $pago->id }}</td>
                                        <td class="text-start ps-4">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $pago->socio->apellidos }} {{ $pago->socio->nombres }}</span>
                                                <span class="text-xs text-secondary">{{ $pago->socio->cedula }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                                Año {{ $pago->anio_pagado }}
                                            </span>
                                        </td>
                                        <td class="text-secondary text-sm fw-bold">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                        <td><span class="fs-6 fw-bold text-dark">${{ number_format($pago->monto, 2) }}</span></td>
                                        <td>
                                            <form action="{{ route('pagos.destroy', $pago) }}" method="POST" onsubmit="return confirm('¿Eliminar pago permanentemente?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0 m-0"><i class="fas fa-trash-alt"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-5 text-center text-muted">No hay pagos registrados.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer py-3">{{ $pagos->links() }}</div>
            </div>
        </div>

        {{-- ========================================================= --}}
        {{-- ESTO ES LO QUE TE FALTABA: EL CONTENEDOR DEL MODAL --}}
        {{-- ========================================================= --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    {{-- Aquí se carga el contenido vía AJAX --}}
                </div>
            </div>
        </div>

        <x-app.footer />

        {{-- ========================================================= --}}
        {{-- Y ESTO ES EL CEREBRO QUE HACE QUE EL BOTÓN FUNCIONE --}}
        {{-- ========================================================= --}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // 1. Detectar el modal
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);

                // 2. Escuchar clics en el botón negro
                document.body.addEventListener('click', function (e) {
                    const btn = e.target.closest('.open-modal');
                    if (btn) {
                        e.preventDefault(); // Evitar recargas
                        
                        // Mostrar spinner de carga
                        modalEl.querySelector('.modal-content').innerHTML = `
                            <div class="p-5 text-center">
                                <div class="spinner-border text-primary"></div>
                                <div class="mt-2 text-muted small">Cargando...</div>
                            </div>`;
                        
                        modal.show(); // Abrir ventana

                        // Cargar la vista (Create o Index)
                        fetch(btn.getAttribute('data-url'))
                            .then(r => r.text())
                            .then(html => {
                                modalEl.querySelector('.modal-content').innerHTML = html;
                                
                                // Reactivar scripts que vengan dentro del HTML cargado (Importante para el buscador)
                                modalEl.querySelectorAll("script").forEach(oldScript => {
                                    const newScript = document.createElement("script");
                                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                    newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                                    oldScript.parentNode.replaceChild(newScript, oldScript);
                                });
                            })
                            .catch(err => {
                                console.error(err);
                                modalEl.querySelector('.modal-content').innerHTML = '<div class="p-4 text-danger text-center">Error al cargar.</div>';
                            });
                    }
                });
            });
        </script>

    </main>
</x-app-layout>