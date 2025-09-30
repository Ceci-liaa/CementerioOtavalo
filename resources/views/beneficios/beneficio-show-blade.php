<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Detalle del Beneficio</h4>
        <div class="d-flex gap-2">
          <a href="{{ route('beneficios.edit', $beneficio) }}" class="btn btn-warning">Editar</a>
          <a href="{{ route('beneficios.index') }}" class="btn btn-secondary">Volver</a>
        </div>
      </div>

      <div class="card"><div class="card-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="text-muted d-block">Nombre</label>
            <div class="fw-semibold">{{ $beneficio->nombre }}</div>
          </div>
          <div class="col-md-3">
            <label class="text-muted d-block">Tipo</label>
            <div class="fw-semibold"><span class="badge bg-secondary">{{ $beneficio->tipo }}</span></div>
          </div>
          <div class="col-md-3">
            <label class="text-muted d-block">Valor</label>
            <div class="fw-semibold">{{ is_null($beneficio->valor) ? '—' : number_format($beneficio->valor, 2) }}</div>
          </div>

          <div class="col-12">
            <label class="text-muted d-block">Descripción</label>
            <div class="fw-semibold">{{ $beneficio->descripcion ?? '—' }}</div>
          </div>

          <div class="col-md-3">
            <label class="text-muted d-block">Creado</label>
            <div class="fw-semibold">{{ $beneficio->created_at?->format('d/m/Y H:i') }}</div>
          </div>
          <div class="col-md-3">
            <label class="text-muted d-block">Actualizado</label>
            <div class="fw-semibold">{{ $beneficio->updated_at?->format('d/m/Y H:i') }}</div>
          </div>
        </div>
      </div></div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
