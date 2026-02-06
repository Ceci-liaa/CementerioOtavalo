{{-- ESTILOS PARA QUE EL BUSCADOR NO SE ESCONDA --}}
<style>
    .ts-dropdown, .ts-control { z-index: 99999 !important; }
    .ts-control { padding: 0.375rem 0.75rem; }
</style>

{{-- LIBRERÍAS TOMSELECT --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">
        <i class="fa-solid fa-pen-to-square me-2"></i> Editar Socio
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('socios.update', $socio) }}">
    @csrf @method('PUT')

    <div class="modal-body">
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- NAVEGACIÓN TABS --}}
        <ul class="nav nav-tabs nav-fill mb-3" id="editTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-personal" type="button">
                    <i class="fas fa-user me-1"></i> Personal
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-contacto" type="button">
                    <i class="fas fa-map-marker-alt me-1"></i> Ubicación
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-membresia" type="button">
                    <i class="fas fa-file-invoice me-1"></i> Membresía
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            {{-- TAB 1: PERSONAL --}}
            <div class="tab-pane fade show active" id="edit-personal">
                <div class="row g-3">
                    {{-- Código (Solo Lectura) --}}
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small">Código</label>
                        <input value="{{ $socio->codigo }}" class="form-control bg-light" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Cédula <span class="text-danger">*</span></label>
                        <input name="cedula" value="{{ old('cedula', $socio->cedula) }}" class="form-control" required
                               maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold small">Nombres <span class="text-danger">*</span></label>
                        <input name="nombres" value="{{ old('nombres', $socio->nombres) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Apellidos <span class="text-danger">*</span></label>
                        <input name="apellidos" value="{{ old('apellidos', $socio->apellidos) }}" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Fecha Nacimiento <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac', optional($socio->fecha_nac)->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Género</label>
                        <select name="genero_id" class="form-select">
                            <option value="">—</option>
                            @foreach($generos as $g)
                                <option value="{{ $g->id }}" @selected(old('genero_id', $socio->genero_id)==$g->id)>{{ $g->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Estado Civil</label>
                        <select name="estado_civil_id" class="form-select" required>
                            @foreach($estados as $e)
                                <option value="{{ $e->id }}" @selected(old('estado_civil_id', $socio->estado_civil_id)==$e->id)>{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- TAB 2: UBICACIÓN (CON BUSCADOR) --}}
            <div class="tab-pane fade" id="edit-contacto">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold small">Comunidad <span class="text-danger">*</span></label>
                        {{-- ID único para TomSelect --}}
                        <select id="select_comunidad_edit" name="comunidad_id" required>
                            @foreach($comunidades as $c)
                                <option value="{{ $c->id }}" @selected(old('comunidad_id', $socio->comunidad_id)==$c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold small">Dirección</label>
                        <input name="direccion" value="{{ old('direccion', $socio->direccion) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Teléfono</label>
                        <input name="telefono" value="{{ old('telefono', $socio->telefono) }}" class="form-control"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Email</label>
                        <input type="email" name="email" value="{{ old('email', $socio->email) }}" class="form-control">
                    </div>
                </div>
            </div>

            {{-- TAB 3: MEMBRESÍA --}}
            <div class="tab-pane fade" id="edit-membresia">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary small">Fecha Inscripción</label>
                        <input type="date" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', optional($socio->fecha_inscripcion)->format('Y-m-d')) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary small">Beneficio</label>
                        <select name="tipo_beneficio" class="form-select" required>
                            <option value="sin_subsidio" @selected($socio->tipo_beneficio == 'sin_subsidio')>Sin Subsidio</option>
                            <option value="con_subsidio" @selected($socio->tipo_beneficio == 'con_subsidio')>Con Subsidio</option>
                            <option value="exonerado" @selected($socio->tipo_beneficio == 'exonerado')>Exonerado</option>
                        </select>
                    </div>

                    <div class="col-12"><hr class="my-2 text-muted"></div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Condición</label>
                        <select name="condicion" class="form-select" required>
                            <option value="ninguna" @selected($socio->condicion == 'ninguna')>Ninguna</option>
                            <option value="discapacidad" @selected($socio->condicion == 'discapacidad')>Discapacidad</option>
                            <option value="enfermedad_terminal" @selected($socio->condicion == 'enfermedad_terminal')>Enfermedad Terminal</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Estatus</label>
                        <select name="estatus" class="form-select" required>
                            <option value="vivo" @selected($socio->estatus == 'vivo')>Vivo</option>
                            <option value="fallecido" @selected($socio->estatus == 'fallecido')>Fallecido</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning fw-bold">Actualizar</button>
    </div>
</form>

{{-- SCRIPT PARA ACTIVAR EL BUSCADOR EN COMUNIDAD (EDIT) --}}
<script>
    new TomSelect("#select_comunidad_edit", {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Buscar comunidad...",
        plugins: ['dropdown_input'],
    });
</script>