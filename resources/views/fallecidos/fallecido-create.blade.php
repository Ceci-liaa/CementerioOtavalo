{{-- ESTILOS PARA TOMSELECT --}}
<style>
    .ts-dropdown, .ts-control { z-index: 99999 !important; }
    .ts-control { padding: 0.375rem 0.75rem; }
</style>

{{-- LIBRERÍAS TOMSELECT --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">
        <i class="fa-solid fa-user-plus me-2"></i> Nuevo Fallecido
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('fallecidos.store') }}">
    @csrf
    
    <div class="modal-body">
        
        {{-- ALERTA --}}
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código se genera automáticamente.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- TABS (AHORA SOLO 2) --}}
        <ul class="nav nav-tabs nav-fill mb-3" id="createTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold small" data-bs-toggle="tab" data-bs-target="#personal" type="button">
                    <i class="fas fa-user me-1"></i> Personal
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#detalles" type="button">
                    <i class="fas fa-clipboard-list me-1"></i> Detalles y Notas
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            {{-- TAB 1: DATOS PERSONALES --}}
            <div class="tab-pane fade show active" id="personal">
                <div class="row g-3">
                    {{-- Cédula: Numérica, 10 dígitos, Obligatoria --}}
                    <div class="col-12">
                        <label class="form-label fw-bold small">Cédula <span class="text-danger">*</span></label>
                        <input type="text" name="cedula" value="{{ old('cedula') }}" class="form-control" 
                               required maxlength="10" placeholder="Solo números"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nombres <span class="text-danger">*</span></label>
                        <input name="nombres" value="{{ old('nombres') }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Apellidos <span class="text-danger">*</span></label>
                        <input name="apellidos" value="{{ old('apellidos') }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Género</label>
                        <select name="genero_id" class="form-select">
                            <option value="">Seleccionar...</option>
                            @foreach($generos as $g)
                                <option value="{{ $g->id }}" @selected(old('genero_id')==$g->id)>{{ $g->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Estado Civil</label>
                        <select name="estado_civil_id" class="form-select">
                            <option value="">Seleccionar...</option>
                            @foreach($estados as $e)
                                <option value="{{ $e->id }}" @selected(old('estado_civil_id')==$e->id)>{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- TAB 2: DETALLES Y NOTAS (FUSIONADO) --}}
            <div class="tab-pane fade" id="detalles">
                <div class="row g-3">
                    {{-- BUSCADOR DE COMUNIDAD --}}
                    <div class="col-12">
                        <label class="form-label fw-bold small">Comunidad / Lugar Fallecimiento</label>
                        <select id="select_comunidad_create" name="comunidad_id" placeholder="Buscar comunidad...">
                            <option value="">Seleccione o escriba...</option>
                            @foreach($comunidades as $c)
                                <option value="{{ $c->id }}" @selected(old('comunidad_id')==$c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-primary">Fecha Nacimiento</label>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-danger">Fecha Fallecimiento</label>
                        <input type="date" name="fecha_fallecimiento" value="{{ old('fecha_fallecimiento') }}" class="form-control">
                    </div>

                    {{-- OBSERVACIONES INTEGRADAS AQUÍ --}}
                    <div class="col-12">
                        <hr class="text-muted opacity-25 my-2">
                        <label class="form-label fw-bold small">Observaciones / Notas</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Ingrese detalles adicionales aquí...">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success fw-bold">Guardar Registro</button>
    </div>
</form>

<script>
    new TomSelect("#select_comunidad_create", {
        create: false,
        sortField: { field: "text", direction: "asc" },
        plugins: ['dropdown_input'],
    });
</script>