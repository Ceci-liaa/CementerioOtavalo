<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Detalle del Socio</h4>
        <div class="d-flex gap-2">
          <a href="{{ route('socios.edit', $socio) }}" class="btn btn-warning">Editar</a>
          <a href="{{ route('socios.index') }}" class="btn btn-secondary">Volver</a>
        </div>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="text-muted d-block">Cédula</label>
              <div class="fw-semibold">{{ $socio->cedula }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Apellidos</label>
              <div class="fw-semibold">{{ $socio->apellidos }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Nombres</label>
              <div class="fw-semibold">{{ $socio->nombres }}</div>
            </div>

            <div class="col-md-4">
              <label class="text-muted d-block">Género</label>
              <div class="fw-semibold">{{ $socio->genero?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Estado civil</label>
              <div class="fw-semibold">{{ $socio->estadoCivil?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Fecha de nacimiento</label>
              <div class="fw-semibold">{{ optional($socio->fecha_nac)->format('d/m/Y') ?? '—' }}</div>
            </div>

            <div class="col-md-4">
              <label class="text-muted d-block">Comunidad</label>
              <div class="fw-semibold">{{ $socio->comunidad?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Parroquia</label>
              <div class="fw-semibold">{{ $socio->comunidad?->parroquia?->nombre ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Cantón</label>
              <div class="fw-semibold">{{ $socio->comunidad?->parroquia?->canton?->nombre ?? '—' }}</div>
            </div>

            <div class="col-md-4">
              <label class="text-muted d-block">Teléfono</label>
              <div class="fw-semibold">{{ $socio->telefono ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Email</label>
              <div class="fw-semibold">{{ $socio->email ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Dirección</label>
              <div class="fw-semibold">{{ $socio->direccion ?? '—' }}</div>
            </div>

            <div class="col-md-4">
              <label class="text-muted d-block">Representante</label>
              <div class="fw-semibold">
                {!! $socio->es_representante ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}
              </div>
            </div>

            <div class="col-md-4">
              <label class="text-muted d-block">Creado por</label>
              <div class="fw-semibold">{{ $socio->creador?->name ?? '—' }}</div>
            </div>
            <div class="col-md-4">
              <label class="text-muted d-block">Creado el</label>
              <div class="fw-semibold">{{ $socio->created_at?->format('d/m/Y H:i') }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
