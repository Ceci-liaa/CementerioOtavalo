<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="mt-4 row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Historial de Auditoría</strong>
                    </div>

                    <div class="card w-100">
                        <div class="card-body p-3">
                            <!-- Filtros por fecha -->
                            <form method="GET" action="{{ route('auditoria.index') }}" class="mb-4 row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label for="fecha" class="form-label">🔍 Fecha específica:</label>
                                    <input type="date" name="fecha" id="fecha" value="{{ request('fecha') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label for="fecha_inicio" class="form-label">📆 Desde:</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label for="fecha_fin" class="form-label">📆 Hasta:</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}" class="form-control">
                                </div>

                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-50">Filtrar</button>
                                    <a href="{{ route('auditoria.index') }}" class="btn btn-secondary w-50">Limpiar</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover text-center align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Modelo</th>
                                            <th>ID Afectado</th>
                                            <th>Evento</th>
                                            <th>Usuario</th>
                                            <th>Antes</th>
                                            <th>Después</th>
                                            <th>Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($audits as $audit)
                                            <tr>
                                                <td>{{ $audit->id }}</td>
                                                <td>{{ class_basename($audit->auditable_type) }}</td>
                                                <td>{{ $audit->auditable_id }}</td>
                                                <td><span class="badge bg-info text-dark">{{ strtoupper($audit->event) }}</span></td>
                                                <td>
                                                    {{ optional($audit->user)->name ?? 'Sistema / Anónimo' }}
                                                </td>
                                                <td style="text-align: left; font-size: 0.8rem;">
                                                    <pre class="m-0">{{ json_encode($audit->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </td>
                                                <td style="text-align: left; font-size: 0.8rem;">
                                                    <pre class="m-0">{{ json_encode($audit->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                </td>
                                                <td>{{ $audit->created_at->format('d/m/Y H:i:s') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8">No hay registros de auditoría.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                {{ $audits->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-app.footer />
    </main>
</x-app-layout>
