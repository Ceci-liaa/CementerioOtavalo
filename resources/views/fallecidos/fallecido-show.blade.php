<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Detalle del Registro</h4>
        <div class="d-flex gap-2">
          <a href="{{ route('fallecidos.edit', $fallecido) }}" class="btn btn-warning">Editar</a>
          <a href="{{ route('fallecidos.index') }}" class="btn btn-secondary">Volver</a>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="text-muted d-block">Cédula</label>
              <div class="fw-semibold">{{ $fallecido->cedula }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Apellidos</label>
              <div class="fw-semibold">{{ $fallecido->apellidos }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Nombres</label>
              <div class="fw-semibold">{{ $fallecido->nombres }}</div>
            </div>

            <div class="col-md-4">
              <label class="text-muted d-block">Género</label>
              <div class="fw-semibold">{{ $fallecido->genero?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Estado civil</label>
              <div class="fw-semibold">{{ $fallecido->estadoCivil?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Fecha de nacimiento</label>
              <div class="fw-semibold">{{ optional($fallecido->fecha_nacimiento)->format('d/m/Y') ?? '—' }}</div>
            </div>

            <div class="col-md-4">
              <label class="text-muted d-block">Comunidad</label>
              <div class="fw-semibold">{{ $fallecido->comunidad?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Parroquia</label>
              <div class="fw-semibold">{{ $fallecido->comunidad?->parroquia?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Cantón</label>
              <div class="fw-semibold">{{ $fallecido->comunidad?->parroquia?->canton?->nombre ?? '—' }}</div>
            </div>

            <div class="col-md-6">
              <label class="text-muted d-block">Fecha de fallecimiento</label>
              <div class="fw-semibold">{{ optional($fallecido->fecha_fallecimiento)->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="col-md-6">
              <label class="text-muted d-block">Creado por</label>
              <div class="fw-semibold">{{ $fallecido->creador?->name ?? '—' }}</div>
            </div>

            <div class="col-12">
              <label class="text-muted d-block">Observaciones</label>
              <div class="fw-semibold">{{ $fallecido->observaciones ?? '—' }}</div>
            </div>

            <div class="col-md-6">
              <label class="text-muted d-block">Creado</label>
              <div class="fw-semibold">{{ $fallecido->created_at?->format('d/m/Y H:i') }}</div>
            </div>
            <div class="col-md-6">
              <label class="text-muted d-block">Actualizado</label>
              <div class="fw-semibold">{{ $fallecido->updated_at?->format('d/m/Y H:i') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
