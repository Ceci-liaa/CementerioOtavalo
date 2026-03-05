<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detalle e Historial del Nicho</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row g-3">
        <div class="col-12 bg-light p-3 rounded border mb-2 d-flex justify-content-between align-items-center">
             <div><small class="text-muted d-block">Código Nicho</small><span class="fw-bold text-primary fs-5">{{ $nicho->codigo }}</span></div>
             <div class="text-end"><span class="badge {{ $nicho->estado == 'DISPONIBLE' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $nicho->estado }}</span></div>
        </div>

        <div class="col-12 border-bottom pb-2 mb-2"><h6 class="fw-bold mb-0">Socio Titular Actual</h6></div>
        @if($nicho->socios->isNotEmpty())
            @php $socio = $nicho->socios->first(); @endphp
            <div class="col-md-8 fw-semibold">{{ $socio->apellidos }} {{ $socio->nombres }}</div>
            <div class="col-md-4 text-end badge bg-secondary">{{ $socio->pivot->rol }}</div>
        @else
            <div class="col-12 text-muted">-- Sin responsable --</div>
        @endif

        <div class="col-12 mt-3">
            <h6 class="fw-bold border-bottom pb-2">Historial de Ocupantes</h6>
            <div class="table-responsive">
                <table class="table table-sm table-bordered text-center align-middle" style="font-size: 0.8rem;">
                    <thead class="table-light">
                        <tr>
                            <th>Fallecido</th>
                            <th>Socio Responsable</th> {{-- NUEVO CAMPO AGREGADO --}}
                            <th>Inhumación</th>
                            <th>Exhumación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nicho->fallecidos as $f)
                            <tr>
                                <td class="text-start ps-2">
                                    <small class="text-muted d-block">{{ $f->pivot->codigo }}</small>
                                    {{ $f->apellidos }} {{ $f->nombres }}
                                </td>
                                <td>
                                    {{-- Buscamos el socio guardado en el pivote --}}
                                    @php $res = \App\Models\Socio::find($f->pivot->socio_id); @endphp
                                    {{ $res ? ($res->apellidos . ' ' . $res->nombres) : 'N/A' }}
                                </td>
                                <td>{{ optional($f->pivot->fecha_inhumacion)->format('d/m/Y') }}</td>
                                <td>
                                    @if($f->pivot->fecha_exhumacion)
                                        <span class="text-danger fw-bold">{{ $f->pivot->fecha_exhumacion->format('d/m/Y') }}</span>
                                    @else - @endif
                                </td>
                                <td>
                                    @if(!$f->pivot->fecha_exhumacion)
                                        @can('eliminar asignacion')
                                        <form action="{{ route('asignacion.destroy', [$nicho->identificacion, $f->id]) }}" 
                                              method="POST" class="d-inline form-delete-fallecido">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1" 
                                                    title="Quitar de este nicho">
                                                <i class="fa-solid fa-trash" style="font-size: 0.65rem;"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted">Sin historial.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer justify-content-between">
    {{-- Verificamos si hay un código para buscar --}}
    @if(!empty($nicho->codigo))
        <a href="https://visor-cementerio.onrender.com?buscar={{ urlencode($nicho->codigo) }}" 
           target="_blank" 
           class="btn btn-info text-white shadow-sm">
            <i class="fas fa-map-location-dot me-2"></i> Ubicar en Mapa
        </a>
    @else
        <button class="btn btn-secondary" disabled>Sin Ubicación</button>
    @endif

    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
</div>

<script>
    document.querySelectorAll('.form-delete-fallecido').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Quitar este fallecido del nicho?',
                text: 'Se eliminará la asignación. Podrás reasignarlo a otro nicho después.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>