{{-- CABECERA DEL MODAL --}}
<div class="modal-header bg-warning text-dark py-2">
    <h5 class="modal-title fw-bold" style="font-size: 1.1rem;">
        <i class="fas fa-pen me-2"></i>Editar Borrador {{ $factura->codigo }}
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

{{-- FORMULARIO --}}
<form method="POST" action="{{ route('facturas.update', $factura) }}" id="formEditFactura" class="d-flex flex-column" style="height: calc(100vh - 200px); min-height: 400px;">
    @csrf @method('PUT')

    {{-- BODY CON SCROLL --}}
    <div class="modal-body p-0" style="overflow-y: auto;">
        <div class="container-fluid py-3">
            
            {{-- Errores --}}
            @if ($errors->any())
                <div class="alert alert-danger py-2 text-xs mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. DATOS DEL CLIENTE --}}
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-light py-2 border-bottom">
                    <h6 class="mb-0 fw-bold text-dark small text-uppercase">1. Datos del Cliente</h6>
                </div>
                <div class="card-body py-2 px-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-0 text-muted">Nombres *</label>
                            <input name="cliente_nombre" value="{{ old('cliente_nombre', $factura->cliente_nombre) }}" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold mb-0 text-muted">Apellidos</label>
                            <input name="cliente_apellido" value="{{ old('cliente_apellido', $factura->cliente_apellido) }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-0 text-muted">Cédula</label>
                            <input name="cliente_cedula" value="{{ old('cliente_cedula', $factura->cliente_cedula) }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-0 text-muted">Teléfono</label>
                            <input name="cliente_telefono" value="{{ old('cliente_telefono', $factura->cliente_telefono) }}" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold mb-0 text-muted">Fecha Emisión</label>
                            <input type="date" name="fecha" value="{{ old('fecha', $factura->fecha->format('Y-m-d')) }}" class="form-control form-control-sm" required>
                        </div>
                        {{-- Email oculto pero editable si quisieras mostrarlo --}}
                        <input type="hidden" name="cliente_email" value="{{ $factura->cliente_email }}">
                    </div>
                </div>
            </div>

            {{-- 2. DETALLE DE ÍTEMS (EDITABLE) --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold small text-uppercase">2. Detalle de Ítems</h6>
                    <button type="button" class="btn btn-sm btn-light text-dark fw-bold" id="btn_add_linea_edit">
                        <i class="fa fa-plus me-1"></i> Agregar
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
                            <tbody id="tbody_items_edit">
                                {{-- AQUÍ SE CARGAN LOS ÍTEMS EXISTENTES Y NUEVOS --}}
                            </tbody>
                            <tfoot class="border-top bg-white">
                                <tr>
                                    <td colspan="4" class="text-end fw-bold pe-3 py-2 text-secondary">TOTAL ACTUALIZADO:</td>
                                    <td class="text-end fw-bold py-3 fs-5 text-dark" id="total_display_edit">$ 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div id="empty_msg_edit" class="text-center py-4 text-muted" style="display: none;">
                        <i class="fas fa-exclamation-circle mb-1"></i> Sin ítems. Agregue al menos uno.
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- FOOTER --}}
    <div class="modal-footer bg-light py-2 border-top shadow-sm mt-auto">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-warning px-4 fw-bold shadow-sm">
            Actualizar Factura
        </button>
    </div>
</form>

<script>
    (function() {
        // 1. DATOS DESDE PHP
        const catalogo = {
            beneficio: @json($beneficios),
            servicio: @json($servicios)
        };
        // Detalles existentes para precargar
        const detallesExistentes = @json($factura->detalles);

        const tbody = document.getElementById('tbody_items_edit');
        const emptyMsg = document.getElementById('empty_msg_edit');
        const totalDisplay = document.getElementById('total_display_edit');

        // 2. FUNCIÓN CREAR FILA
        function crearFila(data = null) {
            emptyMsg.style.display = 'none';
            const tr = document.createElement('tr');
            
            // Valores iniciales (si es edición o nuevo)
            let tipoInicial = '';
            let idInicial = '';
            let precioInicial = '0.00';
            let cantInicial = 1;

            if(data) {
                // Si viene de BD
                tipoInicial = (data.beneficio_id) ? 'beneficio' : 'servicio';
                idInicial = data.beneficio_id || data.servicio_id;
                precioInicial = parseFloat(data.precio).toFixed(2);
                cantInicial = data.cantidad;
            }

            tr.innerHTML = `
                <td class="ps-2 py-2">
                    <select class="form-select form-select-sm select-tipo bg-light" required>
                        <option value="">Selecc...</option>
                        <option value="beneficio" ${tipoInicial=='beneficio'?'selected':''}>Beneficio</option>
                        <option value="servicio" ${tipoInicial=='servicio'?'selected':''}>Servicio</option>
                    </select>
                    <input type="hidden" name="items[beneficio_id][]" class="input-beneficio" value="${tipoInicial=='beneficio'?idInicial:''}">
                    <input type="hidden" name="items[servicio_id][]" class="input-servicio" value="${tipoInicial=='servicio'?idInicial:''}">
                </td>
                <td class="py-2">
                    <select class="form-select form-select-sm select-item" required ${!data ? 'disabled' : ''}>
                        <option value="">← Elija tipo</option>
                    </select>
                </td>
                <td class="py-2">
                    <input type="number" name="items[cantidad][]" class="form-control form-control-sm text-center input-cant" value="${cantInicial}" min="1" required>
                </td>
                <td class="py-2">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text px-1 text-muted">$</span>
                        <input type="number" name="items[precio][]" class="form-control text-end input-precio px-1" step="0.01" value="${precioInicial}" required>
                    </div>
                </td>
                <td class="text-end align-middle fw-bold text-dark span-subtotal pe-2">$ 0.00</td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-link text-danger p-0 btn-delete"><i class="fas fa-times"></i></button>
                </td>
            `;

            tbody.appendChild(tr);
            activarLogica(tr, tipoInicial, idInicial);
        }

        // 3. LÓGICA DE EVENTOS POR FILA
        function activarLogica(row, tipoPre = '', idPre = '') {
            const selTipo = row.querySelector('.select-tipo');
            const selItem = row.querySelector('.select-item');
            const inBeneficio = row.querySelector('.input-beneficio');
            const inServicio = row.querySelector('.input-servicio');
            const inCant = row.querySelector('.input-cant');
            const inPrecio = row.querySelector('.input-precio');
            const spanSub = row.querySelector('.span-subtotal');
            const btnDel = row.querySelector('.btn-delete');

            // Función para llenar el select de items
            function llenarItems(tipo, seleccionado = null) {
                selItem.innerHTML = '<option value="">Seleccione ítem...</option>';
                if(tipo && catalogo[tipo]) {
                    selItem.disabled = false;
                    catalogo[tipo].forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.text = item.nombre;
                        opt.setAttribute('data-precio', item.valor);
                        if(seleccionado == item.id) opt.selected = true;
                        selItem.appendChild(opt);
                    });
                } else {
                    selItem.disabled = true;
                }
            }

            // Si es precarga, llenar items iniciales
            if(tipoPre) llenarItems(tipoPre, idPre);

            // Cambio TIPO
            selTipo.addEventListener('change', function() {
                inPrecio.value = '0.00';
                inBeneficio.value = '';
                inServicio.value = '';
                llenarItems(this.value);
                calcular();
            });

            // Cambio ITEM
            selItem.addEventListener('change', function() {
                const tipo = selTipo.value;
                const opt = this.options[this.selectedIndex];
                const precio = opt.getAttribute('data-precio') || 0;
                
                // Actualizar inputs hidden
                if(tipo === 'beneficio') { inBeneficio.value = this.value; inServicio.value = ''; }
                else { inServicio.value = this.value; inBeneficio.value = ''; }

                // Poner precio sugerido solo si el precio actual es 0 (para no sobreescribir ediciones manuales si las hubiera, aunque aquí forzamos reseteo al cambiar item)
                inPrecio.value = parseFloat(precio).toFixed(2);
                calcular();
            });

            // Cálculos
            function calcular() {
                const c = parseFloat(inCant.value) || 0;
                const p = parseFloat(inPrecio.value) || 0;
                const sub = c * p;
                spanSub.textContent = "$ " + sub.toFixed(2);
                calcTotalGlobal();
            }

            inCant.addEventListener('input', calcular);
            inPrecio.addEventListener('input', calcular);
            
            // Eliminar
            btnDel.addEventListener('click', function() {
                row.remove();
                if(tbody.children.length === 0) emptyMsg.style.display = 'block';
                calcTotalGlobal();
            });

            // Calc inicial
            calcular();
        }

        function calcTotalGlobal() {
            let total = 0;
            document.querySelectorAll('#tbody_items_edit tr').forEach(r => {
                const c = parseFloat(r.querySelector('.input-cant').value) || 0;
                const p = parseFloat(r.querySelector('.input-precio').value) || 0;
                total += (c * p);
            });
            totalDisplay.textContent = "$ " + total.toFixed(2);
        }

        // 4. INICIALIZAR
        document.getElementById('btn_add_linea_edit').addEventListener('click', () => crearFila());

        // Cargar datos existentes
        if(detallesExistentes && detallesExistentes.length > 0) {
            detallesExistentes.forEach(det => crearFila(det));
        } else {
            crearFila(); // Una vacía si no hay nada (raro)
        }

    })();
</script>