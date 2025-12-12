{{-- CABECERA (Amarilla para distinguir Editar) --}}
<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">Editar Socio</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('socios.update', $socio) }}">
    @csrf @method('PUT')

    <div class="modal-body">
        
        {{-- Mostrar errores dentro del modal --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Código (Solo lectura) --}}
            <div class="col-12">
                <label class="form-label fw-bold text-muted">Código</label>
                <input value="{{ $socio->codigo }}" class="form-control bg-light" readonly>
            </div>

            {{-- Fila 1 --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Nombres <span class="text-danger">*</span></label>
                <input name="nombres" value="{{ old('nombres', $socio->nombres) }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Apellidos <span class="text-danger">*</span></label>
                <input name="apellidos" value="{{ old('apellidos', $socio->apellidos) }}" class="form-control" required>
            </div>

            {{-- Fila 2 --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Cédula <span class="text-danger">*</span></label>
                <input name="cedula" value="{{ old('cedula', $socio->cedula) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Género</label>
                <select name="genero_id" class="form-select">
                    <option value="">—</option>
                    @foreach($generos as $g)
                        <option value="{{ $g->id }}" @selected(old('genero_id', $socio->genero_id)==$g->id)>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Estado Civil</label>
                <select name="estado_civil_id" class="form-select">
                    <option value="">—</option>
                    @foreach($estados as $e)
                        <option value="{{ $e->id }}" @selected(old('estado_civil_id', $socio->estado_civil_id)==$e->id)>{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fila 3 --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Comunidad</label>
                <select name="comunidad_id" class="form-select">
                    <option value="">—</option>
                    @foreach($comunidades as $c)
                        <option value="{{ $c->id }}" @selected(old('comunidad_id', $socio->comunidad_id)==$c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Dirección</label>
                <input name="direccion" value="{{ old('direccion', $socio->direccion) }}" class="form-control">
            </div>

            {{-- Fila 4 --}}
            <div class="col-md-4">
                <label class="form-label fw-bold">Teléfono</label>
                <input name="telefono" value="{{ old('telefono', $socio->telefono) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" value="{{ old('email', $socio->email) }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Fecha Nacimiento</label>
                <input type="date" name="fecha_nac" value="{{ old('fecha_nac', optional($socio->fecha_nac)->format('Y-m-d')) }}" class="form-control">
            </div>

            {{-- Checkbox --}}
            <div class="col-12 mt-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="repEdit" name="es_representante" value="1" @checked(old('es_representante', $socio->es_representante))>
                    <label class="form-check-label user-select-none" for="repEdit">Es representante</label>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        {{-- Data-bs-dismiss cierra el modal sin recargar --}}
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning">Actualizar</button>
    </div>
</form>