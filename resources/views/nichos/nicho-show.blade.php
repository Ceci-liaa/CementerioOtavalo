<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Detalle del Nicho</h4>
        <div class="d-flex gap-2">
          <a href="{{ route('nichos.edit',$nicho) }}" class="btn btn-warning">Editar</a>
          <a href="{{ route('nichos.index') }}" class="btn btn-secondary">Volver</a>
        </div>
      </div>

      <div class="card"><div class="card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="text-muted d-block">Bloque</label>
            <div class="fw-semibold">
              {{ $nicho->bloque?->codigo }} — {{ $nicho->bloque?->nombre }}
            </div>
          </div>
          <div class="col-md-4">
            <label class="text-muted d-block">Código</label>
            <div class="fw-semibold">{{ $nicho->codigo }}</div>
          </div>
          <div class="col-md-4">
            <label class="text-muted d-block">Capacidad</label>
            <div class="fw-semibold">{{ $nicho->capacidad }}</div>
          </div>

          <div class="col-md-4">
            <label class="text-muted d-block">Estado</label>
            <div class="fw-semibold text-capitalize">{{ $nicho->estado }}</div>
          </div>
          <div class="col-md-4">
            <label class="text-muted d-block">Disponible</label>
            <div class="fw-semibold">{!! $nicho->disponible ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}</div>
          </div>
          <div class="col-md-4">
            <label class="text-muted d-block">QR UUID</label>
            <div class="fw-semibold">{{ $nicho->qr_uuid ?? '—' }}</div>
          </div>

          <div class="col-12">
            <label class="text-muted d-block">Geom (JSON)</label>
            <pre class="bg-light p-3 rounded" style="white-space:pre-wrap">{{ $nicho->geom ? json_encode($nicho->geom, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) : '—' }}</pre>
          </div>

          <div class="col-md-6">
            <label class="text-muted d-block">Creado por</label>
            <div class="fw-semibold">{{ $nicho->creador?->name ?? '—' }}</div>
          </div>
          <div class="col-md-3">
            <label class="text-muted d-block">Creado</label>
            <div class="fw-semibold">{{ $nicho->created_at?->format('d/m/Y H:i') }}</div>
          </div>
          <div class="col-md-3">
            <label class="text-muted d-block">Actualizado</label>
            <div class="fw-semibold">{{ $nicho->updated_at?->format('d/m/Y H:i') }}</div>
          </div>
        </div>
      </div></div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
