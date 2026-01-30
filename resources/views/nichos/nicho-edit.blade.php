<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Registro de Nicho</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('nichos.update', $nicho) }}">
    @csrf @method('PUT')
    
    <div class="modal-body">
        
        <div class="row g-3">
            {{-- Código (Visual) --}}
            <div class="col-md-12">
                <label class="form-label fw-bold">Código Actual</label>
                <input name="codigo" value="{{ old('codigo', $nicho->codigo) }}" class="form-control bg-light" readonly>
                <small class="text-muted text-xs">Para cambiar el código, selecciona otra ubicación en el mapa.</small>
            </div>

            {{-- SELECCIÓN DE MAPA --}}
            <div class="col-12">
                <label class="form-label fw-bold text-primary">Vincular con Mapa (GIS)</label>
                <select name="nicho_geom_id" class="form-select">
                    <option value="">-- Sin Mapa (Manual) --</option>
                    @isset($nichosGeom)
                        @foreach($nichosGeom as $ng)
                            <option value="{{ $ng->id }}" @selected(old('nicho_geom_id', $nicho->nicho_geom_id) == $ng->id)>
                                {{ $ng->codigo }}
                            </option>
                        @endforeach
                    @endisset
                </select>
            </div>

            {{-- Bloque --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Bloque <span class="text-danger">*</span></label>
                <select name="bloque_id" class="form-select" required>
                    @foreach($bloques as $b)
                        <option value="{{ $b->id }}" @selected(old('bloque_id', $nicho->bloque_id)==$b->id)>{{ $b->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Socio --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Socio Titular</label>
                <select name="socio_id" class="form-select">
                    <option value="">-- Sin asignar --</option>
                    @foreach($socios as $s)
                        <option value="{{ $s->id }}" @selected(old('socio_id', $nicho->socio_id)==$s->id)>
                             {{ $s->apellidos ?? $s->apellido ?? '' }} {{ $s->nombres ?? $s->nombre ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Resto de campos iguales... --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Tipo</label>
                <select name="tipo_nicho" class="form-select" required>
                    <option value="PROPIO" @selected(old('tipo_nicho', $nicho->tipo_nicho) == 'PROPIO')>PROPIO</option>
                    <option value="COMPARTIDO" @selected(old('tipo_nicho', $nicho->tipo_nicho) == 'COMPARTIDO')>COMPARTIDO</option>
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">Capacidad</label>
                <input type="number" min="1" name="capacidad" value="{{ old('capacidad',$nicho->capacidad) }}" class="form-control" required>
            </div>
            
            <div class="col-md-12">
                <label class="form-label fw-bold">Estado</label>
                <select name="estado" class="form-select" required>
                    @foreach(['disponible','ocupado','mantenimiento'] as $e)
                        <option value="{{ $e }}" @selected(old('estado',$nicho->estado)==$e)>{{ ucfirst($e) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion', $nicho->descripcion) }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>