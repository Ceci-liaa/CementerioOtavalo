{{-- ESTILOS PARA QUE EL BUSCADOR NO SE ESCONDA DETRÁS DEL MODAL --}}
<style>
    .ts-dropdown, .ts-control { z-index: 99999 !important; }
    .ts-control { padding: 0.375rem 0.75rem; }
</style>

{{-- LIBRERÍAS (Si no están en tu layout principal) --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Socio</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('socios.store') }}">
    @csrf
    
    <div class="modal-body">
        
        {{-- ALERTA ESTILO ORIGINAL (AZUL) --}}
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código de socio se genera automáticamente.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- NAVEGACIÓN TABS --}}
        <ul class="nav nav-tabs nav-fill mb-3" id="socioTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold" data-bs-toggle="tab" data-bs-target="#personal" type="button">
                    <i class="fas fa-user me-1"></i> Personal
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#contacto" type="button">
                    <i class="fas fa-map-marker-alt me-1"></i> Ubicación
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold" data-bs-toggle="tab" data-bs-target="#membresia" type="button">
                    <i class="fas fa-file-invoice me-1"></i> Membresía
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            {{-- TAB 1: PERSONAL (REDISEÑADO) --}}
            <div class="tab-pane fade show active" id="personal">
                <div class="row g-3">
                    
                    {{-- FILA 1: Cédula (Pequeña) + Nombres + Apellidos --}}
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Cédula <span class="text-danger">*</span></label>
                        <input type="text" name="cedula" value="{{ old('cedula') }}" class="form-control" 
                               required maxlength="10" placeholder="10 dígitos"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold small">Nombres <span class="text-danger">*</span></label>
                        <input name="nombres" value="{{ old('nombres') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Apellidos <span class="text-danger">*</span></label>
                        <input name="apellidos" value="{{ old('apellidos') }}" class="form-control" required>
                    </div>

                    {{-- FILA 2: Fecha, Género, Civil --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Fecha Nacimiento <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Género</label>
                        <select name="genero_id" class="form-select">
                            <option value="">Seleccionar...</option>
                            @foreach($generos as $g)
                                <option value="{{ $g->id }}" @selected(old('genero_id')==$g->id)>{{ $g->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Estado Civil <span class="text-danger">*</span></label>
                        <select name="estado_civil_id" class="form-select" required>
                            <option value="">Seleccionar...</option>
                            @foreach($estados as $e)
                                <option value="{{ $e->id }}" @selected(old('estado_civil_id')==$e->id)>{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- TAB 2: UBICACIÓN (CON BUSCADOR) --}}
            <div class="tab-pane fade" id="contacto">
                <div class="row g-3">
                    
                    {{-- COMUNIDAD CON BUSCADOR INTELIGENTE --}}
                    <div class="col-12">
                        <label class="form-label fw-bold">Comunidad de Residencia <span class="text-danger">*</span></label>
                        <select id="select_comunidad" name="comunidad_id" required placeholder="Escriba para buscar comunidad...">
                            <option value="">Seleccione o escriba...</option>
                            @foreach($comunidades as $c)
                                <option value="{{ $c->id }}" @selected(old('comunidad_id')==$c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-bold small">Dirección</label>
                        <input name="direccion" value="{{ old('direccion') }}" class="form-control" placeholder="Calle y número">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Teléfono</label>
                        <input name="telefono" value="{{ old('telefono') }}" class="form-control" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control">
                    </div>
                </div>
            </div>

            {{-- TAB 3: MEMBRESÍA --}}
            <div class="tab-pane fade" id="membresia">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary small">Fecha Inscripción <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_inscripcion" value="{{ old('fecha_inscripcion', date('Y-m-d')) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary small">Beneficio Inicial <span class="text-danger">*</span></label>
                        <select name="tipo_beneficio" class="form-select" required>
                            <option value="sin_subsidio">Sin Subsidio</option>
                            <option value="con_subsidio">Con Subsidio</option>
                            <option value="exonerado">Exonerado (+75)</option>
                        </select>
                    </div>
                    
                    <div class="col-12"><hr class="text-muted my-2"></div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Condición <span class="text-danger">*</span></label>
                        <select name="condicion" class="form-select" required>
                            <option value="ninguna">Ninguna</option>
                            <option value="discapacidad">Discapacidad</option>
                            <option value="enfermedad_terminal">Enfermedad Terminal</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Estatus <span class="text-danger">*</span></label>
                        <select name="estatus" class="form-select" required>
                            <option value="vivo">Vivo</option>
                            <option value="fallecido">Fallecido</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success fw-bold">Guardar</button>
    </div>
</form>

{{-- SCRIPT PARA ACTIVAR EL BUSCADOR EN COMUNIDAD --}}
<script>
    new TomSelect("#select_comunidad", {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Buscar comunidad...",
        plugins: ['dropdown_input'], // Permite escribir dentro del menú
    });
</script>