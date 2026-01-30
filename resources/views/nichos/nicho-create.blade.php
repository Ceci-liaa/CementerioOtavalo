<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Registro de Nicho</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('nichos.store') }}">
    @csrf
    <div class="modal-body">
        
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> 
            Si seleccionas una <b>Ubicación en Mapa</b>, el código se copiará automáticamente.
        </div>

        <div class="row g-3">
            
            {{-- SELECCIÓN DE MAPA (NUEVO) --}}
            <div class="col-12">
                <label class="form-label fw-bold text-primary">Ubicación en Mapa (Código GIS)</label>
                <select name="nicho_geom_id" class="form-select select2">
                    <option value="">-- Generar Código Automático (Manual) --</option>
                    @isset($nichosGeom)
                        @foreach($nichosGeom as $ng)
                            {{-- Muestra: B8-NB97 --}}
                            <option value="{{ $ng->id }}" @selected(old('nicho_geom_id') == $ng->id)>
                                {{ $ng->codigo }}
                            </option>
                        @endforeach
                    @endisset
                </select>
                <small class="text-muted text-xs">Ej: B14-C01-N1 (Viene del mapa)</small>
            </div>

            {{-- Bloque --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Bloque Físico <span class="text-danger">*</span></label>
                <select name="bloque_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($bloques as $b)
                        <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Socio --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Socio Titular</label>
                <select name="socio_id" class="form-select">
                    <option value="">-- Sin asignar --</option>
                    @foreach($socios as $s)
                        <option value="{{ $s->id }}">
                             {{ $s->apellidos ?? $s->apellido ?? '' }} {{ $s->nombres ?? $s->nombre ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tipo --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Tipo <span class="text-danger">*</span></label>
                <select name="tipo_nicho" class="form-select" required>
                    <option value="PROPIO">PROPIO</option>
                    <option value="COMPARTIDO">COMPARTIDO</option>
                </select>
            </div>

            {{-- Capacidad --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Capacidad <span class="text-danger">*</span></label>
                <input type="number" name="capacidad" min="1" value="1" class="form-control" required>
            </div>

            {{-- Estado --}}
            <div class="col-md-12">
                <label class="form-label fw-bold">Estado <span class="text-danger">*</span></label>
                <select name="estado" class="form-select" required>
                    <option value="disponible">Disponible</option>
                    <option value="ocupado">Ocupado</option>
                    <option value="mantenimiento">Mantenimiento</option>
                </select>
            </div>

            {{-- Descripción --}}
            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>