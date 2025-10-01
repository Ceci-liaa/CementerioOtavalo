<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Nuevo Beneficio</h4>
        <a href="{{ route('beneficios.index') }}" class="btn btn-secondary">Volver</a>
      </div>

      @if ($errors->any())
        <div class="alert alert-danger">
          <b>Revisa los campos:</b>
          <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('beneficios.store') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre *</label>
              <input name="nombre" value="{{ old('nombre') }}" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Tipo *</label>
              <input name="tipo" value="{{ old('tipo') }}" class="form-control" maxlength="10" required>
              <small class="text-muted">Ej.: FEE, DESC, OTRO</small>
            </div>
            <div class="col-md-3">
              <label class="form-label">Valor</label>
              <input type="number" name="valor" value="{{ old('valor') }}" step="0.01" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" rows="3" class="form-control">{{ old('descripcion') }}</textarea>
            </div>
          </div>

          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary">Guardar</button>
            <a href="{{ route('beneficios.index') }}" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div></div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
