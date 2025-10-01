<x-app-layout>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <x-app.navbar />

    <div class="px-5 py-4 container-fluid">
      <div class="row"><div class="col-12">

        <div class="alert alert-dark text-sm"><strong style="font-size:24px;">Gestión de Servicios</strong></div>

        @if(session('success')) <div id="ok-msg" class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error'))   <div id="err-msg" class="alert alert-danger">{{ session('error') }}</div> @endif

        <div class="d-flex flex-wrap gap-2 mb-3 align-items-end">
          <a href="{{ route('servicios.create') }}" class="btn btn-success">
            <i class="fa-solid fa-plus"></i> Nuevo Servicio
          </a>

          <form method="GET" class="d-flex flex-wrap gap-2 ms-auto">
            <div>
              <label class="form-label mb-1">Buscar</label>
              <input name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Nombre o descripción">
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-secondary mt-4">Filtrar</button>
              <a href="{{ route('servicios.index') }}" class="btn btn-link mt-4">Limpiar</a>
            </div>
          </form>
        </div>

        <div class="card"><div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle text-center">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Valor</th>
                  <th style="width:180px;">Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($servicios as $s)
                  <tr>
                    <td>{{ $s->id }}</td>
                    <td class="text-start">{{ $s->nombre }}</td>
                    <td>{{ is_null($s->valor) ? '—' : number_format($s->valor,2) }}</td>
                    <td>
                      <a href="{{ route('servicios.show',$s) }}" class="btn btn-sm btn-info" title="Ver">
                        <i class="fa-solid fa-eye"></i>
                      </a>
                      <a href="{{ route('servicios.edit',$s) }}" class="btn btn-sm btn-warning" title="Editar">
                        <i class="fa-solid fa-pen-to-square"></i>
                      </a>
                      <form action="{{ route('servicios.destroy',$s) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('¿Eliminar servicio {{ $s->nombre }}?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" title="Eliminar">
                          <i class="fa-solid fa-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="4" class="text-muted py-4">No hay servicios.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-3">{{ $servicios->links() }}</div>
        </div></div>

      </div></div>
    </div>

    <x-app.footer />
  </main>

  <script>
    document.addEventListener("DOMContentLoaded",()=>['ok-msg','err-msg'].forEach(id=>{const el=document.getElementById(id);if(!el)return;setTimeout(()=>{el.style.transition="opacity .5s";el.style.opacity=0;setTimeout(()=>el.remove(),500)},5000)}));
  </script>
</x-app-layout>
