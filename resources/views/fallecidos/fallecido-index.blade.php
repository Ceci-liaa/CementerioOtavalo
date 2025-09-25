<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="alert alert-dark text-sm">
        <strong style="font-size:22px">Gestión de Fallecidos</strong>
      </div>

      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

      <div class="d-flex gap-2 mb-3">
        <a href="{{ route('fallecidos.create') }}" class="btn btn-success">
          <i class="fa fa-plus"></i> Nuevo Registro
        </a>

        <form method="GET" class="d-flex gap-2 ms-auto" style="max-width:620px">
          <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por cédula, apellidos o nombres…">
          <button class="btn btn-outline-secondary">Buscar</button>
          <a href="{{ route('fallecidos.index') }}" class="btn btn-link">Limpiar</a>
        </form>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle text-center">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Cédula</th>
                  <th>Apellidos</th>
                  <th>Nombres</th>
                  <th>Comunidad</th>
                  <th>Parroquia</th>
                  <th>Cantón</th>
                  <th>Fallecimiento</th>
                  <th style="width:170px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($fallecidos as $f)
                  <tr>
                    <td>{{ $f->id }}</td>
                    <td>{{ $f->cedula }}</td>
                    <td>{{ $f->apellidos }}</td>
                    <td>{{ $f->nombres }}</td>
                    <td>{{ $f->comunidad?->nombre }}</td>
                    <td>{{ $f->comunidad?->parroquia?->nombre }}</td>
                    <td>{{ $f->comunidad?->parroquia?->canton?->nombre }}</td>
                    <td>{{ optional($f->fecha_fallecimiento)->format('d/m/Y') }}</td>
                    <td>
                      <a href="{{ route('fallecidos.show',$f) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                      <a href="{{ route('fallecidos.edit',$f) }}" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a>
                      <form action="{{ route('fallecidos.destroy',$f) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar el registro de {{ $f->apellidos }} {{ $f->nombres }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="9" class="text-muted py-4">No hay registros.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">{{ $fallecidos->links() }}</div>
        </div>
      </div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
