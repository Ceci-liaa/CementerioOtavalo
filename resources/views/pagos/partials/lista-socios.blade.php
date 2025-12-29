@forelse($resultados as $socio)
    <a href="javascript:void(0);" 
       class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 border-bottom open-modal"
       data-url="{{ route('pagos.index', $socio->id) }}"> {{-- ESTO ABRE EL MODAL DE COBRO --}}
        
        <div>
            <div class="fw-bold text-dark fs-6">{{ $socio->apellidos }} {{ $socio->nombres }}</div>
            <div class="small text-muted">
                <i class="fas fa-id-card me-1"></i> {{ $socio->cedula }}
                <span class="mx-1">|</span>
                <i class="fas fa-map-marker-alt me-1"></i> {{ $socio->comunidad?->nombre ?? 'Sin comunidad' }}
            </div>
        </div>
        
        <div>
            <button class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                Cobrar <i class="fas fa-arrow-right ms-1"></i>
            </button>
        </div>
    </a>
@empty
    <div class="text-center p-4 text-muted">
        <i class="fas fa-user-slash fa-2x mb-2 opacity-50"></i><br>
        No se encontró ningún socio con esos datos.
    </div>
@endforelse