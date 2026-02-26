{{-- ESTRUCTURA DEL MODAL: HEADER (Fijo) + BODY (Scroll) + FOOTER (Fijo) --}}

{{-- 1. HEADER --}}
<div class="modal-header bg-dark text-white py-2">
    <h5 class="modal-title fw-bold" style="font-size: 1.1rem;">
        <i class="fas fa-file-invoice-dollar me-2"></i>Nueva Factura
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('facturas.store') }}" id="formFactura" class="d-flex flex-column" style="height: calc(100vh - 200px); min-height: 400px;">
    @csrf

    {{-- 2. BODY CON SCROLL (overflow-y: auto) --}}
    <div class="modal-body p-0" style="overflow-y: auto;">
        
        <div class="container-fluid py-3">
            {{-- AVISO --}}
            <div class="alert alert-light border-start border-primary border-4 py-2 mb-3 shadow-sm">
                <div class="d-flex align-items-center small">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    <span><strong>Automático:</strong> El código de factura se generará al guardar.</span>
                </div>
            </div>

            {{-- SECCIÓN DATOS CLIENTE --}}
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-light py-2 border-bottom">
                    <h6 class="mb-0 fw-bold text-primary small text-uppercase">1. Datos del Cliente</h6>
                </div>
                <div class="card-body py-2 px-3">
                    {{-- Buscador Socio con autocompletado --}}
                    <div class="mb-2 position-relative" id="socio_search_wrapper">
                        <label class="form-label small fw-bold mb-1 text-muted">
                            <i class="fas fa-search me-1"></i>Buscar Socio
                        </label>
                        <input type="text" id="socio_search_input" class="form-control form-control-sm border-secondary-subtle"
                               placeholder="Escriba nombre, apellido o cédula para buscar..." autocomplete="off">
                        <input type="hidden" name="socio_id" id="select_socio" value="">
                        
                        {{-- Dropdown de resultados --}}
                        <div id="socio_dropdown" class="position-absolute w-100 bg-white border rounded-bottom shadow-sm" 
                             style="display:none; max-height:200px; overflow-y:auto; z-index:1055;">
                            <div class="socio-option px-3 py-2 border-bottom text-muted small" data-id="" 
                                 data-nombre="" data-apellido="" data-cedula="" data-email="" data-telefono=""
                                 style="cursor:pointer;">
                                <i class="fas fa-user-edit me-1"></i> -- Cliente Particular (Llenado manual) --
                            </div>
                            @foreach ($socios as $socio)
                                <div class="socio-option px-3 py-2 border-bottom" data-id="{{ $socio->id }}"
                                     data-nombre="{{ $socio->nombres }}"
                                     data-apellido="{{ $socio->apellidos }}"
                                     data-cedula="{{ $socio->cedula }}"
                                     data-email="{{ $socio->email }}"
                                     data-telefono="{{ $socio->telefono }}"
                                     style="cursor:pointer; font-size:0.85rem;">
                                    👤 <strong>{{ $socio->apellidos }} {{ $socio->nombres }}</strong> 
                                    <span class="text-muted">({{ $socio->cedula }})</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Badge del socio seleccionado --}}
                        <div id="socio_selected_badge" class="mt-2" style="display:none;">
                            <div class="d-inline-flex align-items-center rounded-pill px-2 py-1 shadow-sm"
                                 style="background: linear-gradient(135deg, #1a7f37, #2da44e); color: #fff; font-size: 0.78rem; font-weight: 600;">
                                <i class="fas fa-user-check me-2" style="font-size:1rem;"></i>
                                <span id="socio_badge_text"></span>
                                <button type="button" class="btn-close btn-close-white ms-3" style="font-size:0.6rem;" id="socio_clear_btn" title="Quitar socio"></button>
                            </div>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-0 text-muted">Nombres *</label>
                            <input name="cliente_nombre" id="in_nombre" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-0 text-muted">Apellidos</label>
                            <input name="cliente_apellido" id="in_apellido" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-0 text-muted">Cédula/RUC</label>
                            <input name="cliente_cedula" id="in_cedula" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-0 text-muted">Teléfono</label>
                            <input name="cliente_telefono" id="in_telefono" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-0 text-muted">Fecha Emisión</label>
                            <input type="date" name="fecha" value="{{ date('Y-m-d') }}" class="form-control form-control-sm" required>
                        </div>
                        <input type="hidden" name="cliente_email" id="in_email">
                    </div>
                </div>
            </div>

            {{-- SECCIÓN DETALLE (Tabla Editable) --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold small text-uppercase">2. Detalle de Ítems</h6>
                    <button type="button" class="btn btn-sm btn-light text-primary fw-bold" id="btn_add_linea">
                        <i class="fa fa-plus me-1"></i> Agregar Línea
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle mb-0" style="min-width: 600px;">
                            <thead class="bg-light text-secondary">
                                <tr style="font-size: 0.8rem;">
                                    <th style="width: 130px;" class="ps-3">Tipo</th>
                                    <th>Descripción / Ítem</th>
                                    <th style="width: 80px;" class="text-center">Cant.</th>
                                    <th style="width: 100px;" class="text-end">Precio</th>
                                    <th style="width: 110px;" class="text-end">Subtotal</th>
                                    <th style="width: 40px;"></th>
                                </tr>
                            </thead>
                            <tbody id="tbody_items">
                                {{-- Las filas se generan con JS --}}
                            </tbody>
                            <tfoot class="border-top bg-white">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold py-3 pe-3 text-secondary">TOTAL A PAGAR:</td>
                                    <td class="text-end fw-bold py-3 fs-5 text-dark" id="total_display">$ 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    {{-- Mensaje vacío --}}
                    <div id="empty_msg" class="text-center py-4 text-muted">
                        <i class="fas fa-arrow-up mb-2"></i><br>
                        Pulsa <b>"Agregar Línea"</b> para comenzar.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. FOOTER (Fijo) --}}
    <div class="modal-footer bg-light py-2 border-top shadow-sm mt-auto">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm">
            <i class="fas fa-save me-2"></i> Guardar Factura
        </button>
    </div>
</form>

{{-- SCRIPTS --}}
<script>
    // 1. PREPARAMOS LOS DATOS (Arrays de Laravel a JS)
    const catalogo = {
        beneficio: @json($beneficios),
        servicio: @json($servicios)
    };

    const tbody = document.getElementById('tbody_items');
    const emptyMsg = document.getElementById('empty_msg');
    const totalDisplay = document.getElementById('total_display');

    // 2. FUNCIÓN PARA AGREGAR LÍNEA (Editable)
    function agregarLinea() {
        emptyMsg.style.display = 'none';

        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td class="ps-2 py-2">
                <select class="form-select form-select-sm select-tipo bg-light" required>
                    <option value="">Selecc...</option>
                    <option value="beneficio">Beneficio</option>
                    <option value="servicio">Servicio</option>
                </select>
                <input type="hidden" name="items[beneficio_id][]" class="input-beneficio">
                <input type="hidden" name="items[servicio_id][]" class="input-servicio">
            </td>
            <td class="py-2">
                <select class="form-select form-select-sm select-item" disabled required>
                    <option value="">← Elija tipo</option>
                </select>
            </td>
            <td class="py-2">
                <input type="number" name="items[cantidad][]" class="form-control form-control-sm text-center input-cant" value="1" min="1" required>
            </td>
            <td class="py-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text px-1 text-muted">$</span>
                    <input type="number" name="items[precio][]" class="form-control text-end input-precio px-1" step="0.01" value="0.00" required>
                </div>
            </td>
            <td class="text-end align-middle fw-bold text-dark span-subtotal pe-2">
                $ 0.00
            </td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-link text-danger p-0 btn-delete" title="Quitar fila">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;

        tbody.appendChild(tr);
        activarLogicaFila(tr);
    }

    // 3. LÓGICA DE CADA FILA (Eventos)
    function activarLogicaFila(row) {
        const selTipo = row.querySelector('.select-tipo');
        const selItem = row.querySelector('.select-item');
        const inBeneficio = row.querySelector('.input-beneficio');
        const inServicio = row.querySelector('.input-servicio');
        const inCant = row.querySelector('.input-cant');
        const inPrecio = row.querySelector('.input-precio');
        const spanSub = row.querySelector('.span-subtotal');
        const btnDel = row.querySelector('.btn-delete');

        // A. Cambio de TIPO
        selTipo.addEventListener('change', function() {
            const tipo = this.value;
            
            // Resetear inputs
            selItem.innerHTML = '<option value="">Seleccione ítem...</option>';
            selItem.disabled = true;
            inPrecio.value = "0.00";
            inBeneficio.value = "";
            inServicio.value = "";

            if (tipo && catalogo[tipo]) {
                selItem.disabled = false;
                catalogo[tipo].forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.text = item.nombre;
                    opt.setAttribute('data-precio', item.valor); // Asumimos columna 'valor'
                    selItem.appendChild(opt);
                });
            }
            calcularFila();
        });

        // B. Cambio de ÍTEM
        selItem.addEventListener('change', function() {
            const op = this.options[this.selectedIndex];
            const precio = op.getAttribute('data-precio') || 0;
            const tipo = selTipo.value;

            // Poner precio sugerido
            inPrecio.value = parseFloat(precio).toFixed(2);

            // Asignar ID al input hidden correcto
            if(tipo === 'beneficio') {
                inBeneficio.value = this.value;
                inServicio.value = "";
            } else {
                inServicio.value = this.value;
                inBeneficio.value = "";
            }
            calcularFila();
        });

        // C. Cálculos
        function calcularFila() {
            const c = parseFloat(inCant.value) || 0;
            const p = parseFloat(inPrecio.value) || 0;
            const sub = c * p;
            spanSub.textContent = "$ " + sub.toFixed(2);
            actualizarTotalGlobal();
        }

        inCant.addEventListener('input', calcularFila);
        inPrecio.addEventListener('input', calcularFila);

        // D. Eliminar
        btnDel.addEventListener('click', function() {
            row.remove();
            if(tbody.children.length === 0) emptyMsg.style.display = 'block';
            actualizarTotalGlobal();
        });
    }

    // 4. TOTAL GLOBAL
    function actualizarTotalGlobal() {
        let total = 0;
        document.querySelectorAll('#tbody_items tr').forEach(row => {
            const c = parseFloat(row.querySelector('.input-cant').value) || 0;
            const p = parseFloat(row.querySelector('.input-precio').value) || 0;
            total += (c * p);
        });
        totalDisplay.textContent = "$ " + total.toFixed(2);
    }

    // 5. INICIAR (Evento Botón y Cliente)
    document.getElementById('btn_add_linea').addEventListener('click', agregarLinea);

    // ============================================
    // BUSCADOR DE SOCIOS (Autocompletado)
    // ============================================
    const socioInput = document.getElementById('socio_search_input');
    const socioHidden = document.getElementById('select_socio');
    const socioDropdown = document.getElementById('socio_dropdown');
    const socioBadge = document.getElementById('socio_selected_badge');
    const socioBadgeText = document.getElementById('socio_badge_text');
    const socioClearBtn = document.getElementById('socio_clear_btn');
    const allOptions = socioDropdown.querySelectorAll('.socio-option');

    // Mostrar dropdown y filtrar al escribir
    socioInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        socioDropdown.style.display = 'block';
        let visibles = 0;

        allOptions.forEach(opt => {
            const nombre = (opt.dataset.nombre || '').toLowerCase();
            const apellido = (opt.dataset.apellido || '').toLowerCase();
            const cedula = (opt.dataset.cedula || '').toLowerCase();
            const texto = nombre + ' ' + apellido + ' ' + cedula;

            if (!opt.dataset.id && query === '') {
                opt.style.display = 'block'; // Siempre mostrar "Cliente Particular" si no hay búsqueda
                visibles++;
            } else if (texto.includes(query) || query === '') {
                opt.style.display = 'block';
                visibles++;
            } else {
                opt.style.display = 'none';
            }
        });

        if (visibles === 0) {
            socioDropdown.style.display = 'none';
        }
    });

    // Mostrar dropdown al enfocar
    socioInput.addEventListener('focus', function() {
        socioDropdown.style.display = 'block';
        // Mostrar todos si el campo está vacío
        if (this.value.trim() === '') {
            allOptions.forEach(opt => opt.style.display = 'block');
        }
    });

    // Hover en opciones
    allOptions.forEach(opt => {
        opt.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#e9ecef';
        });
        opt.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });

        // Seleccionar socio
        opt.addEventListener('click', function() {
            const id = this.dataset.id;
            socioHidden.value = id;
            socioDropdown.style.display = 'none';

            if (id) {
                // Llenar campos del cliente
                document.getElementById('in_nombre').value = this.dataset.nombre;
                document.getElementById('in_apellido').value = this.dataset.apellido;
                document.getElementById('in_cedula').value = this.dataset.cedula;
                document.getElementById('in_telefono').value = this.dataset.telefono;
                document.getElementById('in_email').value = this.dataset.email;

                // Mostrar badge
                socioBadgeText.textContent = this.dataset.apellido + ' ' + this.dataset.nombre + ' (' + this.dataset.cedula + ')';
                socioBadge.style.display = 'block';
                socioInput.value = '';
                socioInput.placeholder = 'Socio seleccionado ✓ — escriba para buscar otro';
            } else {
                // Cliente particular: limpiar campos
                document.getElementById('in_nombre').value = '';
                document.getElementById('in_apellido').value = '';
                document.getElementById('in_cedula').value = '';
                document.getElementById('in_telefono').value = '';
                document.getElementById('in_email').value = '';
                socioBadge.style.display = 'none';
                socioInput.value = '';
                socioInput.placeholder = 'Escriba nombre, apellido o cédula para buscar...';
            }
        });
    });

    // Botón limpiar socio seleccionado
    socioClearBtn.addEventListener('click', function() {
        socioHidden.value = '';
        socioBadge.style.display = 'none';
        socioInput.placeholder = 'Escriba nombre, apellido o cédula para buscar...';
        document.getElementById('in_nombre').value = '';
        document.getElementById('in_apellido').value = '';
        document.getElementById('in_cedula').value = '';
        document.getElementById('in_telefono').value = '';
        document.getElementById('in_email').value = '';
    });

    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!document.getElementById('socio_search_wrapper').contains(e.target)) {
            socioDropdown.style.display = 'none';
        }
    });

    // Agregar una línea por defecto al abrir
    agregarLinea();

</script>