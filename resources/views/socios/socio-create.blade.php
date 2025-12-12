{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Socio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('socios.store') }}">
    @csrf
    
    {{-- CUERPO DEL MODAL --}}
    <div class="modal-body">
        
        {{-- Mensaje informativo --}}
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código de socio se genera automáticamente.
        </div>

        {{-- Mostrar errores si la validación falla dentro del modal --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- Fila 1: Nombres y Apellidos --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Nombres <span class="text-danger">*</span></label>
                <input name="nombres" value="{{ old('nombres') }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Apellidos <span class="text-danger">*</span></label>
                <input name="apellidos" value="{{ old('apellidos') }}" class="form-control" required>
            </div>

            {{-- Fila 2: Cédula y Género --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Cédula <span class="text-danger">*</span></label>
                <input name="cedula" value="{{ old('cedula') }}" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Género</label>
                <select name="genero_id" class="form-select">
                    <option value="">Seleccionar...</option>
                    @foreach($generos as $g)
                        <option value="{{ $g->id }}" @selected(old('genero_id')==$g->id)>{{ $g->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fila 3: Estado Civil y Comunidad --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Estado Civil</label>
                <select name="estado_civil_id" class="form-select">
                    <option value="">Seleccionar...</option>
                    @foreach($estados as $e)
                        <option value="{{ $e->id }}" @selected(old('estado_civil_id')==$e->id)>{{ $e->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Comunidad</label>
                <select name="comunidad_id" class="form-select">
                    <option value="">Seleccionar...</option>
                    @foreach($comunidades as $c)
                        <option value="{{ $c->id }}" @selected(old('comunidad_id')==$c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Fila 4: Dirección y Teléfono --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Dirección</label>
                <input name="direccion" value="{{ old('direccion') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Teléfono</label>
                <input name="telefono" value="{{ old('telefono') }}" class="form-control">
            </div>

            {{-- Fila 5: Email y Fecha Nacimiento --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Fecha de nacimiento</label>
                <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" class="form-control">
            </div>

            {{-- Checkbox Representante --}}
            <div class="col-12 mt-2">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="rep" name="es_representante" value="1" @checked(old('es_representante'))>
                    <label class="form-check-label user-select-none" for="rep">Es representante</label>
                </div>
            </div>
        </div>
    </div>

    {{-- PIE DEL MODAL (Botones) --}}
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>