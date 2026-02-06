{{-- CSS PARA CORREGIR QUE EL SELECT NO SE VEA DETRÁS DEL MODAL --}}
<style>
    /* Hace que el dropdown de TomSelect flote por encima del Modal de Bootstrap */
    .ts-dropdown, .ts-control {
        z-index: 99999 !important; /* Muy alto para ganar al modal */
    }
    /* Ajuste visual para que parezca un input de Bootstrap */
    .ts-control {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
    }
</style>

{{-- LIBRERÍAS (Si no están en el layout) --}}
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nueva Comunidad</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('comunidades.store') }}">
    @csrf
    
    <div class="modal-body">
        
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

        <div class="row g-3">
            {{-- Cantón --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Cantón <span class="text-danger">*</span></label>
                <select id="canton_select" class="form-select" required autocomplete="off">
                    <option value="">Buscar cantón...</option>
                    @foreach(\App\Models\Canton::orderBy('nombre')->get(['id','nombre']) as $c)
                        <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Parroquia --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">Parroquia <span class="text-danger">*</span></label>
                {{-- IMPORTANTE: Quitamos el 'disabled' de aquí y lo manejamos por JS --}}
                <select name="parroquia_id" id="parroquia_select" class="form-select" required autocomplete="off">
                    <option value="">Seleccione un cantón primero...</option>
                </select>
            </div>

            {{-- Nombre --}}
            <div class="col-12">
                <label class="form-label fw-bold">Nombre Comunidad <span class="text-danger">*</span></label>
                <input name="nombre" value="{{ old('nombre') }}" class="form-control" required maxlength="255" placeholder="Ej: San Francisco">
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success">Guardar</button>
    </div>
</form>

{{-- SCRIPT CORREGIDO --}}
<script>
    // Configuración base
    var configTomSelect = {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "Escriba para buscar...",
        plugins: ['dropdown_input'],
        // Esto ayuda a que no se cierre inesperadamente
        closeAfterSelect: true, 
    };

    // 1. Inicializar Cantón
    var selectCanton = new TomSelect("#canton_select", configTomSelect);

    // 2. Inicializar Parroquia
    var selectParroquia = new TomSelect("#parroquia_select", configTomSelect);
    
    // BLOQUEAMOS la parroquia inmediatamente por JS
    selectParroquia.disable(); 

    // 3. Lógica de cambio
    selectCanton.on('change', function(value) {
        
        // Limpiar parroquias anteriores
        selectParroquia.clear();
        selectParroquia.clearOptions();
        
        if (!value) {
            selectParroquia.disable();
            selectParroquia.addOption({value: '', text: 'Seleccione un cantón primero...'});
            selectParroquia.refreshOptions(); // Refrescar visualmente
            return;
        }

        selectParroquia.disable(); // Bloquear mientras carga
        selectParroquia.load('Cargando...'); // Mostrar texto de carga en el input

        // FETCH
        fetch('/parroquias/by-canton/' + value) 
            .then(response => {
                if (!response.ok) throw new Error("Error en la red");
                return response.json();
            })
            .then(data => {
                selectParroquia.enable();
                
                // Agregar las nuevas opciones
                data.forEach(function(p) {
                    selectParroquia.addOption({value: p.id, text: p.nombre});
                });
                
                // Si no hay datos
                if(data.length === 0){
                    selectParroquia.addOption({value: '', text: 'No hay parroquias registradas'});
                }

                // IMPORTANTE: Refrescar las opciones para que TomSelect las pinte
                selectParroquia.refreshOptions(false);
            })
            .catch(error => {
                console.error('Error cargando parroquias:', error);
                selectParroquia.enable();
                selectParroquia.addOption({value: '', text: 'Error al cargar'});
            });
    });
</script>