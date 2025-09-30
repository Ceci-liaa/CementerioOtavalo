<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="alert alert-dark text-sm">
        <strong style="font-size:22px">Gestión de Nichos</strong>
      </div>

      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

      <div class="d-flex gap-2 mb-3">
        <a href="{{ route('nichos.create') }}" class="btn btn-success">
          <i class="fa fa-plus"></i> Nuevo Nicho
        </a>

        <form method="GET" class="d-flex gap-2 ms-auto" style="max-width:760px">
          <input name="q" value="{{ $q ?? request('q') }}" class="form-control" placeholder="Buscar por código…">
          <select name="bloque_id" class="form-select" style="min-width:220px">
            <option value="">Todos los bloques</option>
            @foreach($bloques as $b)
              <option value="{{ $b->id }}" @selected((string)($bloqueId ?? request('bloque_id')) === (string)$b->id)>
                {{ $b->codigo }} — {{ $b->nombre }}
              </option>
            @endforeach
          </select>
          <button class="btn btn-outline-secondary">Buscar</button>
          <a href="{{ route('nichos.index') }}" class="btn btn-link">Limpiar</a>
        </form>
      </div>

      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle text-center">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Bloque</th>
                  <th>Código</th>
                  <th>Capacidad</th>
                  <th>Estado</th>
                  <th>Disponible</th>
                  <th style="width:170px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($nichos as $n)
                  <tr>
                    <td>{{ $n->id }}</td>
                    <td>
                      {{ $n->bloque?->codigo }}<br>
                      <small class="text-muted">{{ $n->bloque?->nombre }}</small>
                    </td>
                    <td class="fw-semibold">{{ $n->codigo }}</td>
                    <td>{{ $n->capacidad }}</td>
                    <td>
                      @switch($n->estado)
                        @case('disponible') <span class="badge bg-success">Disponible</span> @break
                        @case('ocupado')    <span class="badge bg-danger">Ocupado</span>     @break
                        @case('mantenimiento') <span class="badge bg-warning text-dark">Mantenimiento</span> @break
                        @default <span class="badge bg-secondary">{{ $n->estado }}</span>
                      @endswitch
                    </td>
                    <td>{!! $n->disponible ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                    <td>
                      <a href="{{ route('nichos.show',$n) }}" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                      <a href="{{ route('nichos.edit',$n) }}" class="btn btn-sm btn-warning"><i class="fa fa-pen"></i></a>
                      <form action="{{ route('nichos.destroy',$n) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar nicho {{ $n->codigo }} del bloque {{ $n->bloque?->codigo }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="7" class="text-muted py-4">No hay nichos.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">{{ $nichos->links() }}</div>
        </div>
      </div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>

