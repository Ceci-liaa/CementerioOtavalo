<x-app-layout>
    {{-- 1. ESTILOS (Estilo unificado tipo "Cantones") --}}
    <style>
        /* ALERTAS */
        .alert-success { background-color: #e4f4db !important; color: #708736 !important; border-color: #e4f4db !important; font-size: 14px !important; }
        .alert-success .btn-close { filter: none !important; opacity: 0.5; color: #708736; }
        .alert-success .btn-close:hover { opacity: 1; }
        
        .alert-danger { background-color: #fde1e1 !important; color: #cf304a !important; border-color: #fde1e1 !important; font-size: 14px !important; }
        .alert-danger .btn-close { filter: none !important; opacity: 0.5; color: #cf304a; }

        /* INPUTS Y FILTROS */
        .input-group-text { border-color: #dee2e6; }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }
        .compact-filter { width: auto; min-width: 140px; max-width: 180px; }

        /* ESTILOS DE TABLA (Header Oscuro y Tipografía) */
        .table thead th {
            font-size: 14px !important;    
            text-transform: uppercase;    
            letter-spacing: 0.05rem;      
            font-weight: 700 !important;  
            padding-top: 15px !important; 
            padding-bottom: 15px !important; 
        }
        .btn-action { margin-right: 4px; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            
            {{-- 2. ENCABEZADO --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0">
                    <div class="d-flex align-items-center gap-3">
                        <h3 class="font-weight-bolder mb-0" style="color: #1c2a48;">Gestión de Bloques</h3>
                        <span class="badge bg-light text-dark border" style="font-size: 0.8rem;">
                            Total: {{ $bloques->total() }}
                        </span>
                    </div>
                    <p class="mb-0 text-secondary text-sm">Administración de bloques y áreas del cementerio.</p>
                </div>

                {{-- PERMISO: crear bloque --}}
                @can('crear bloque')
                <button type="button" class="btn btn-success px-4 open-modal" 
                        style="height: fit-content;"
                        data-url="{{ route('bloques.create') }}">
                    <i class="fa-solid fa-plus me-2"></i> Nuevo Bloque
                </button>
                @endcan
            </div>

            {{-- 3. ALERTAS (Sistema robusto de validación) --}}
            <div class="mb-3">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show alert-temporal" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show alert-temporal" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <div>
                                <strong>No se pudo guardar:</strong>
                                <ul class="mb-0 ps-3" style="list-style-type: none; padding-left: 0;">
                                    @foreach ($errors->all() as $error)
                                        <li>- {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
            </div>

            {{-- 4. FORMULARIO PRINCIPAL (Envuelve filtros y tabla para Reportes) --}}
            {{-- IMPORTANTE: Se mantiene el form para que funcione la selección múltiple (checkboxes) --}}
            <form action="{{ route('bloques.reports') }}" method="POST" id="reportForm">
                @csrf
                
                {{-- BARRA DE HERRAMIENTAS: Reportes y Buscador --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    
                    {{-- LADO IZQUIERDO: Botón de Reportes --}}
                    @can('reportar bloque')
                    <div class="dropdown w-100 w-md-auto">
                        <button class="btn text-white dropdown-toggle mb-0 px-4 w-100 w-md-auto" 
                                style="background-color: #5ea6f7; border-radius: 6px; font-weight: 600;" 
                                type="button" id="dropdownGenerate" data-bs-toggle="dropdown" aria-expanded="false">
                            Generar Reporte
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownGenerate">
                            <li><button type="submit" name="report_type" value="pdf" class="dropdown-item"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</button></li>
                            <li><button type="submit" name="report_type" value="excel" class="dropdown-item"><i class="fas fa-file-excel text-success me-2"></i> Excel</button></li>
                        </ul>
                    </div>
                    @else
                    <div class="w-100 w-md-auto"></div> {{-- Espaciador si no tiene permiso --}}
                    @endcan

                    {{-- LADO DERECHO: Buscador --}}
                    <div class="d-flex gap-2 w-100 w-md-auto justify-content-end">
                        <div class="input-group input-group-sm bg-white border rounded overflow-hidden compact-filter shadow-sm">
                            <span class="input-group-text bg-white border-0 pe-1 text-secondary"><i class="fas fa-search"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}"
                                   class="form-control border-0 ps-1 shadow-none" 
                                   placeholder="Buscar..." id="searchInput">
                            {{-- Input oculto para mantener la búsqueda al enviar reporte --}}
                            {{-- <input type="hidden" name="q" value="{{ request('q') }}"> --}} 
                            {{-- Nota: Al ser POST el reporte y GET la búsqueda, a veces se maneja separado, pero el input visible sirve si el form envía todo. --}}
                        </div>
                    </div>
                </div>

                {{-- 5. TABLA --}}
                <div class="card shadow-sm border">
                    <div class="card-body p-0 pb-2">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle text-center mb-0">
                                <thead class="bg-dark text-white">
                                    <tr>
                                        {{-- CHECKBOX MASTER --}}
                                        <th class="opacity-10" style="width: 40px;">
                                            @if(auth()->user()->can('eliminar bloque') || auth()->user()->can('reportar bloque'))
                                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()" style="cursor: pointer;">
                                            @endif
                                        </th>
                                        <th class="opacity-10" style="width: 50px;">#</th>
                                        <th class="opacity-10">Código</th>
                                        <th class="opacity-10 text-start ps-4">Nombre</th>
                                        <th class="opacity-10">Área (m²)</th>
                                        <th class="opacity-10">Geometría</th>
                                        <th class="opacity-10" style="width:180px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bloques as $b)
                                        <tr>
                                            {{-- CHECKBOX INDIVIDUAL --}}
                                            <td>
                                                @if(auth()->user()->can('eliminar bloque') || auth()->user()->can('reportar bloque'))
                                                    <input type="checkbox" name="ids[]" value="{{ $b->id }}" class="check-item" style="cursor: pointer;">
                                                @endif
                                            </td>
                                            
                                            <td class="text-sm fw-bold text-secondary">
                                                {{ $bloques->firstItem() + $loop->index }}
                                            </td>

                                            <td class="fw-bold text-dark">{{ $b->codigo }}</td>
                                            
                                            <td class="text-start ps-4">
                                                <span class="text-sm font-weight-bold">{{ $b->nombre }}</span>
                                            </td>
                                            
                                            <td class="text-secondary text-sm">
                                                {{ $b->area_m2 ? number_format($b->area_m2, 2) : '-' }}
                                            </td>
                                            
                                            <td>
                                                @if($b->bloqueGeom || $b->geom)
                                                    <span class="badge border text-success bg-light">Asignada</span>
                                                @else
                                                    <span class="badge border text-secondary bg-light">Sin Asignar</span>
                                                @endif
                                            </td>

                                            {{-- ACCIONES --}}
                                            <td>
                                                <div class="d-flex justify-content-center">
                                                    @can('ver bloque')
                                                    <button type="button" class="btn btn-sm btn-info mb-0 btn-action open-modal" 
                                                            data-url="{{ route('bloques.show', $b) }}" title="Ver">
                                                        <i class="fa-solid fa-eye text-white" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    @endcan

                                                    @can('editar bloque')
                                                    <button type="button" class="btn btn-sm btn-warning mb-0 btn-action open-modal" 
                                                            data-url="{{ route('bloques.edit', $b) }}" title="Editar">
                                                        <i class="fa-solid fa-pen-to-square" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    @endcan

                                                    @can('eliminar bloque')
                                                    <button type="button" class="btn btn-sm btn-danger mb-0 btn-action js-delete-btn"
                                                            data-url="{{ route('bloques.destroy', $b) }}"
                                                            data-item="{{ $b->codigo }} - {{ $b->nombre }}" title="Eliminar">
                                                        <i class="fa-solid fa-trash" style="font-size: 0.7rem;"></i>
                                                    </button>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="7" class="text-center py-5 text-muted">No se encontraron bloques.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        {{-- PAGINACIÓN --}}
                        <div class="mt-3 px-3 d-flex justify-content-end">
                            {{ $bloques->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </form>

            <form id="deleteForm" method="POST" action="" style="display:none;">@csrf @method('DELETE')</form>
        </div>

        {{-- MODAL DINÁMICO --}}
        <div class="modal fade" id="dynamicModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content"></div>
            </div>
        </div>

        <x-app.footer />

        {{-- SCRIPTS --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Alertas Temporales
                setTimeout(() => { 
                    document.querySelectorAll('.alert-temporal').forEach(alert => { 
                        alert.style.transition = "opacity 0.5s"; 
                        alert.style.opacity = 0; 
                        setTimeout(() => alert.remove(), 500); 
                    }); 
                }, 4000);

                // Filtro Buscador (Enter para buscar)
                const searchInput = document.getElementById('searchInput'); 
                if(searchInput) {
                    searchInput.addEventListener('keypress', function (e) { 
                        if (e.key === 'Enter') { 
                            e.preventDefault(); 
                            // Redireccionar GET para busqueda, ya que el form principal es POST para reportes
                            window.location.href = "{{ route('bloques.index') }}?q=" + encodeURIComponent(this.value); 
                        } 
                    });
                }

                // Modal AJAX
                const modalEl = document.getElementById('dynamicModal');
                const modal = new bootstrap.Modal(modalEl);
                document.querySelectorAll('.open-modal').forEach(btn => {
                    btn.addEventListener('click', function () {
                        modalEl.querySelector('.modal-content').innerHTML = '<div class="p-5 text-center"><div class="spinner-border text-primary"></div></div>';
                        modal.show();
                        fetch(this.getAttribute('data-url'))
                            .then(r => r.text())
                            .then(h => { 
                                modalEl.querySelector('.modal-content').innerHTML = h; 
                            });
                    });
                });

                // Lógica de Geometría en Modal (Se mantiene del código original de bloques)
                document.addEventListener('change', async function(e) {
                    if(e.target && e.target.id === 'bloque_geom_id') {
                        const select = e.target;
                        const form = select.closest('form');
                        const geomHidden = form.querySelector('#geom');
                        const id = select.value;
                        if (!id) { if (geomHidden) geomHidden.value = ''; return; }
                        try {
                            let url = "/bloques_geom/" + encodeURIComponent(id) + "/geojson";
                            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                            if (res.ok) {
                                const geo = await res.json();
                                if (geomHidden) geomHidden.value = JSON.stringify(geo);
                            } else {
                                if (geomHidden) geomHidden.value = '';
                            }
                        } catch (err) {
                            console.error(err);
                            if (geomHidden) geomHidden.value = '';
                        }
                    }
                });

                // SweetAlert Eliminar
                document.querySelectorAll('.js-delete-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        Swal.fire({
                            title: '¿Eliminar Bloque?', 
                            html: `¿Deseas eliminar <b>"${this.getAttribute('data-item')}"</b>?`, 
                            icon: 'warning', 
                            showCancelButton: true, 
                            confirmButtonColor: '#d33', 
                            cancelButtonColor: '#3085d6', 
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((r) => { 
                            if (r.isConfirmed) { 
                                const f = document.getElementById('deleteForm'); 
                                f.action = this.getAttribute('data-url'); 
                                f.submit(); 
                            } 
                        });
                    });
                });
            });

            // Función Selección Múltiple
            function toggleSelectAll() { 
                const selectAll = document.getElementById('selectAll');
                if(selectAll){
                    const c = selectAll.checked; 
                    document.querySelectorAll('.check-item').forEach(x => x.checked = c); 
                }
            }
        </script>
    </main>
</x-app-layout>