<x-app-layout>
    <style>
        .table thead th {
            font-size: 13px !important;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            font-weight: 700 !important;
            padding-top: 15px !important;
            padding-bottom: 15px !important;
        }
        .badge { font-weight: 600; text-transform: uppercase; font-size: 0.7rem; }
        pre {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            padding: 8px;
            max-height: 120px;
            overflow-y: auto;
            font-size: 0.75rem;
            text-align: left;
            margin: 0;
            white-space: pre-wrap;
        }
        .form-control:focus, .form-select:focus { border-color: #5ea6f7; box-shadow: 0 0 0 0.2rem rgba(94, 166, 247, 0.25); }

        /* BADGES OSCUROS */
        .badge-created { background-color: #1b5e20 !important; color: white !important; } /* Verde oscuro */
        .badge-updated { background-color: #01579b !important; color: white !important; } /* Azul oscuro */
        .badge-deleted { background-color: #b71c1c !important; color: white !important; } /* Rojo oscuro */
        .badge-default { background-color: #424242 !important; color: white !important; }
    </style>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="font-weight-bolder mb-1" style="color: #1c2a48;">Historial de Auditoría</h3>
                    <p class="mb-0 text-secondary text-sm">Registro detallado de todas las actividades y cambios en el sistema.</p>
                </div>
            </div>

            <div class="card shadow-sm border mb-4">
                <div class="card-body p-3">
                    <!-- Filtros por fecha -->
                    <form method="GET" action="{{ route('auditoria.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label for="fecha" class="form-label fw-bold small">🔍 Fecha específica:</label>
                            <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_inicio" class="form-label fw-bold small">📆 Desde:</label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_fin" class="form-label fw-bold small">📆 Hasta:</label>
                            <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100 mb-0">Filtrar</button>
                            <a href="{{ route('auditoria.index') }}" class="btn btn-secondary btn-sm w-100 mb-0">Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border">
                <div class="card-body p-0 pb-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle text-center mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="opacity-10" style="width: 60px;">ID</th>
                                    <th class="opacity-10">Módulo</th>
                                    <th class="opacity-10">ID Reg.</th>
                                    <th class="opacity-10">Evento</th>
                                    <th class="opacity-10 text-start ps-4">Usuario</th>
                                    <th class="opacity-10" style="width: 25%;">Antes</th>
                                    <th class="opacity-10" style="width: 25%;">Después</th>
                                    <th class="opacity-10">Fecha / Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($audits as $audit)
                                    <tr>
                                        <td class="text-xs fw-bold text-secondary">{{ $audit->id }}</td>
                                        <td class="fw-bold text-dark text-sm">
                                            {{ class_basename($audit->auditable_type) }}
                                        </td>
                                        <td>
                                            <span class="badge border bg-light text-dark">{{ $audit->auditable_id }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match(strtolower($audit->event)) {
                                                    'created' => 'badge-created',
                                                    'updated' => 'badge-updated',
                                                    'deleted' => 'badge-deleted',
                                                    default => 'badge-default'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $audit->event }}</span>
                                        </td>
                                        <td class="text-start ps-4">
                                            <div class="d-flex flex-column">
                                                <span class="text-sm fw-bold text-dark">{{ optional($audit->user)->name ?? 'Sistema' }}</span>
                                                <span class="text-xs text-secondary">{{ optional($audit->user)->email ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td class="p-2">
                                            @if(!empty($audit->old_values))
                                                <pre>{{ json_encode($audit->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                <span class="text-muted text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="p-2">
                                            @if(!empty($audit->new_values))
                                                <pre>{{ json_encode($audit->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                            @else
                                                <span class="text-muted text-xs">—</span>
                                            @endif
                                        </td>
                                        <td class="text-sm fw-bold">
                                            {{ $audit->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-5 text-muted">
                                            No se encontraron registros de auditoría.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3 px-3 d-flex justify-content-center">
                        {{ $audits->links() }}
                    </div>
                </div>
            </div>
        </div>

        <x-app.footer />
    </main>
</x-app-layout>
