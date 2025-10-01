<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="alert alert-dark text-sm">
        <strong style="font-size:22px">Gestión de Beneficios</strong>
      </div>

      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

      <div class="d-flex gap-2 mb-3">
        <a href="{{ route('beneficios.create') }}" class="btn btn-success">
          <i class="fa fa-plus"></i> Nuevo Beneficio
        </a>

        <form method="GET" class="d-flex gap-2 ms-auto" style="max-width:620px">
          <input name="q" value="{{ $q ?? request('q') }}" class="form-control" placeholder="Buscar por nombre, tipo o descripción…">
          <button class="btn btn-outline-secondary">Buscar</button>
          <a href="{{ route('beneficios.index') }}" class="btn btn-link">Limpiar</a>
        </form>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle text-center">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Tipo</th>
                  <th>Valor</th>
                  <th style="width:170px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($beneficios as $b)
                  <tr>
                    <td>{{ $b->id }}</td>
                    <td class="text-start">{{ $b->nombre }}</td>
                    <td><span class="badge bg-secondary">{{ $b->tipo }}</span></td>
                    <td>{{ is_null($b->valor) ? '—' : number_format($b->valor, 2) }}</td>
                    <td>
                      <a href="{{ route('beneficios.show',$b) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                      <a href="{{ route('beneficios.edit',$b) }}" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a>
                      <form action="{{ route('beneficios.destroy',$b) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar beneficio {{ $b->nombre }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="5" class="text-muted py-4">No hay beneficios.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">{{ $beneficios->links() }}</div>
        </div>
      </div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
