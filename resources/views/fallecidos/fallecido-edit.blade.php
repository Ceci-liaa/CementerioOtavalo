{{-- ESTILOS (TomSelect cargado en el index) --}}
<style>
    .ts-dropdown, .ts-control { z-index: 99999 !important; }
    .ts-control { padding: 0.375rem 0.75rem; }
</style>

<div class="modal-header bg-warning text-dark">
    <h5 class="modal-title fw-bold">
        <i class="fa-solid fa-pen-to-square me-2"></i> Editar Fallecido
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('fallecidos.update', $fallecido) }}">
    @csrf @method('PUT')

    <div class="modal-body">
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- TABS (2 PESTAÑAS) --}}
        <ul class="nav nav-tabs nav-fill mb-3" id="editTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-personal" type="button">
                    <i class="fas fa-user me-1"></i> Personal
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link fw-bold small" data-bs-toggle="tab" data-bs-target="#edit-detalles" type="button">
                    <i class="fas fa-clipboard-list me-1"></i> Detalles y Notas
                </button>
            </li>
        </ul>

        <div class="tab-content">
            
            {{-- TAB 1: PERSONAL --}}
            <div class="tab-pane fade show active" id="edit-personal">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold text-muted small">Código Sistema</label>
                        <input value="{{ $fallecido->codigo }}" class="form-control bg-light" readonly>
                    </div>

                    {{-- Cédula Validada --}}
                    <div class="col-12">
                        <label class="form-label fw-bold small">Cédula <span class="text-danger">*</span></label>
                        <input name="cedula" value="{{ old('cedula', $fallecido->cedula) }}" class="form-control" 
                               required maxlength="10" placeholder="Solo números"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Nombres <span class="text-danger">*</span></label>
                        <input name="nombres" value="{{ old('nombres', $fallecido->nombres) }}" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Apellidos <span class="text-danger">*</span></label>
                        <input name="apellidos" value="{{ old('apellidos', $fallecido->apellidos) }}" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Género</label>
                        <select name="genero_id" class="form-select">
                            <option value="">—</option>
                            @foreach($generos as $g)
                                <option value="{{ $g->id }}" @selected(old('genero_id', $fallecido->genero_id) == $g->id)>{{ $g->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">Estado Civil</label>
                        <select name="estado_civil_id" class="form-select">
                            <option value="">—</option>
                            @foreach($estados as $e)
                                <option value="{{ $e->id }}" @selected(old('estado_civil_id', $fallecido->estado_civil_id) == $e->id)>{{ $e->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- TAB 2: DETALLES --}}
            <div class="tab-pane fade" id="edit-detalles">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-bold small">Comunidad</label>
                        <select id="select_comunidad_edit" name="comunidad_id">
                            <option value="">Seleccione...</option>
                            @foreach($comunidades as $c)
                                <option value="{{ $c->id }}" @selected(old('comunidad_id', $fallecido->comunidad_id) == $c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-primary">Fecha Nacimiento</label>
                        <input type="date" name="fecha_nac" value="{{ old('fecha_nac', optional($fallecido->fecha_nac)->format('Y-m-d')) }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-danger">Fecha Fallecimiento</label>
                        <input type="date" name="fecha_fallecimiento" value="{{ old('fecha_fallecimiento', optional($fallecido->fecha_fallecimiento)->format('Y-m-d')) }}" class="form-control">
                    </div>

                    {{-- OBSERVACIONES INTEGRADAS --}}
                    <div class="col-12">
                        <hr class="text-muted opacity-25 my-2">
                        <label class="form-label fw-bold small">Observaciones</label>
                        <textarea name="observaciones" class="form-control" rows="3">{{ old('observaciones', $fallecido->observaciones) }}</textarea>
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

<script>
    new TomSelect("#select_comunidad_edit", {
        create: false,
        sortField: { field: "text", direction: "asc" },
        plugins: ['dropdown_input'],
    });
</script>