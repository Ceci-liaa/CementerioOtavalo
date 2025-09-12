<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="alert alert-dark text-sm">
        <strong style="font-size:22px">Gestión de Socios</strong>
      </div>

      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

      <div class="d-flex gap-2 mb-3">
        <a href="{{ route('socios.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> Nuevo Socio</a>

        <form method="GET" class="d-flex gap-2 ms-auto" style="max-width:620px">
          <input name="q" value="{{ request('q') }}" class="form-control" placeholder="Buscar por nombre/apellido/cedula…">
          <button class="btn btn-outline-secondary">Buscar</button>
          <a href="{{ route('socios.index') }}" class="btn btn-link">Limpiar</a>
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
                  <th>Rep.</th>
                  <th style="width:160px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($socios as $s)
                  <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->cedula }}</td>
                    <td>{{ $s->apellidos }}</td>
                    <td>{{ $s->nombres }}</td>
                    <td>{{ $s->comunidad?->nombre }}</td>
                    <td>{{ $s->comunidad?->parroquia?->nombre }}</td>
                    <td>{{ $s->comunidad?->parroquia?->canton?->nombre }}</td>
                    <td>{!! $s->es_representante ? '<span class="badge bg-success">Sí</span>' : '' !!}</td>
                    <td>
                      <a href="{{ route('socios.show',$s) }}" class="btn btn-sm btn-info"><i class="fa fa-eye" style="font-size:.9rem;"></i></a>
                      <a href="{{ route('socios.edit',$s) }}" class="btn btn-sm btn-warning"><i class="fa fa-pen" style="font-size:.9rem;"></i></a>
                      <form action="{{ route('socios.destroy',$s) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar socio {{ $s->apellidos }} {{ $s->nombres }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash" style="font-size:.9rem;"></i></button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="9" class="text-muted py-4">No hay socios.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">{{ $socios->links() }}</div>
        </div>
      </div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
