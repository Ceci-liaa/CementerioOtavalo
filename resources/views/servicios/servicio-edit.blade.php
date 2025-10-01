<x-app-layout>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <x-app.navbar />

    <div class="px-5 py-4 container-fluid">
      <div class="row"><div class="col-lg-8 mx-auto">

        <div class="alert alert-dark text-sm"><strong style="font-size:24px;">Editar Servicio</strong></div>

        @if ($errors->any())
          <div class="alert alert-danger"><b>Revisa los campos:</b><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
        @endif

        <div class="card"><div class="card-body">
          <form action="{{ route('servicios.update',$servicio) }}" method="POST">
            @csrf @method('PUT')

            <div class="mb-3">
              <label class="form-label">Nombre *</label>
              <input type="text" name="nombre" value="{{ old('nombre',$servicio->nombre) }}" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" class="form-control">{{ old('descripcion',$servicio->descripcion) }}</textarea>
            </div>

            <div class="mb-3">
              <label class="form-label">Valor (sugerido)</label>
              <input type="number" step="0.01" name="valor" value="{{ old('valor',$servicio->valor) }}" class="form-control">
            </div>

            <div class="mt-3 d-flex gap-2">
              <button class="btn btn-primary">Actualizar</button>
              <a href="{{ route('servicios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
          </form>
        </div></div>

      </div></div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
