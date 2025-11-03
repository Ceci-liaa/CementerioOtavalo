<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Nuevo Registro de Fallecido</h4>
        <a href="{{ route('fallecidos.index') }}" class="btn btn-secondary">Volver</a>
      </div>

      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-danger">{{ session('error') }}</div> @endif

      @if ($errors->any())
        <div class="alert alert-danger">
          <b>Revisa los campos:</b>
          <ul class="mb-0">
            @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
          </ul>
        </div>
      @endif

      <div class="card">
        <div class="card-body">
          <form method="POST" action="{{ route('fallecidos.store') }}">
            @csrf

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nombres *</label>
                <input name="nombres" value="{{ old('nombres') }}" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Apellidos *</label>
                <input name="apellidos" value="{{ old('apellidos') }}" class="form-control" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Cédula *</label>
                <input name="cedula" value="{{ old('cedula') }}" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Género</label>
                <select name="genero_id" class="form-select">
                  <option value="">—</option>
                  @foreach($generos as $g)
                    <option value="{{ $g->id }}" @selected(old('genero_id') == $g->id)>{{ $g->nombre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Estado civil</label>
                <select name="estado_civil_id" class="form-select">
                  <option value="">—</option>
                  @foreach($estados as $e)
                    <option value="{{ $e->id }}" @selected(old('estado_civil_id') == $e->id)>{{ $e->nombre }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Comunidad</label>
                <select name="comunidad_id" class="form-select">
                  <option value="">—</option>
                  @foreach($comunidades as $c)
                    <option value="{{ $c->id }}" @selected(old('comunidad_id') == $c->id)>{{ $c->nombre }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-3">
                <label class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nac" value="{{ old('fecha_nac') }}" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">Fecha de fallecimiento</label>
                <input type="date" name="fecha_fallecimiento" value="{{ old('fecha_fallecimiento') }}" class="form-control">
              </div>

              <div class="col-12">
                <label class="form-label">Observaciones</label>
                <textarea name="observaciones" rows="4" class="form-control" placeholder="Notas adicionales…">{{ old('observaciones') }}</textarea>
              </div>
            </div>

            <div class="mt-3 d-flex gap-2">
              <button class="btn btn-primary">Guardar</button>
              <a href="{{ route('fallecidos.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
