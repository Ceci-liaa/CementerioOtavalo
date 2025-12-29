<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold">
        <i class="fas fa-search me-2"></i>Buscar Socio a Cobrar
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body bg-light">
    
    {{-- INPUT DE BÚSQUEDA --}}
    <div class="mb-3 position-relative">
        <div class="input-group input-group-lg border shadow-sm rounded">
            <span class="input-group-text bg-white border-0 text-primary"><i class="fas fa-search"></i></span>
            <input type="text" id="inputBuscarSocio" class="form-control border-0 fw-bold" 
                   placeholder="Escribe cédula, nombre o apellido..." autocomplete="off">
        </div>
        <small class="text-muted ms-2 mt-1 d-block">Escribe para filtrar resultados...</small>
    </div>

    {{-- AQUÍ SE CARGAN LOS RESULTADOS --}}
    <div id="contenedorResultados" class="list-group shadow-sm" style="min-height: 100px;">
        @include('pagos.partials.lista-socios', ['resultados' => $resultados])
    </div>
</div>

<script>
    // Foco automático al abrir
    setTimeout(() => { document.getElementById('inputBuscarSocio').focus(); }, 500);

    const input = document.getElementById('inputBuscarSocio');
    const contenedor = document.getElementById('contenedorResultados');
    let timeout = null;

    input.addEventListener('keyup', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            const query = this.value;
            // Petición AJAX al controlador
            fetch(`{{ route('pagos.create') }}?search=${encodeURIComponent(query)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                contenedor.innerHTML = html;
            });
        }, 300); // Espera 300ms
    });
</script>