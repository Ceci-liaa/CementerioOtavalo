<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Editar Socio</h4>
        <a href="{{ route('socios.index') }}" class="btn btn-secondary">Volver</a>
      </div>

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
          <form method="POST" action="{{ route('socios.update', $socio) }}">
            @csrf @method('PUT')

            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Nombres *</label>
                <input name="nombres" value="{{ old('nombres', $socio->nombres) }}" class="form-control" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Apellidos *</label>
                <input name="apellidos" value="{{ old('apellidos', $socio->apellidos) }}" class="form-control" required>
              </div>

              <div class="col-md-4">
                <label class="form-label">Cédula *</label>
                <input name="cedula" value="{{ old('cedula', $socio->cedula) }}" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">Género</label>
                <select name="genero_id" class="form-select">
                  <option value="">—</option>
                  @foreach($generos as $g)
                    <option value="{{ $g->id }}" @selected(old('genero_id', $socio->genero_id)==$g->id)>{{ $g->nombre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">Estado civil</label>
                <select name="estado_civil_id" class="form-select">
                  <option value="">—</option>
                  @foreach($estados as $e)
                    <option value="{{ $e->id }}" @selected(old('estado_civil_id', $socio->estado_civil_id)==$e->id)>{{ $e->nombre }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Comunidad</label>
                <select name="comunidad_id" class="form-select">
                  <option value="">—</option>
                  @foreach($comunidades as $c)
                    <option value="{{ $c->id }}" @selected(old('comunidad_id', $socio->comunidad_id)==$c->id)>{{ $c->nombre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Dirección</label>
                <input name="direccion" value="{{ old('direccion', $socio->direccion) }}" class="form-control">
              </div>

              <div class="col-md-4">
                <label class="form-label">Teléfono</label>
                <input name="telefono" value="{{ old('telefono', $socio->telefono) }}" class="form-control">
              </div>
              <div class="col-md-4">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $socio->email) }}" class="form-control">
              </div>
              <div class="col-md-4">
                <label class="form-label">Fecha de nacimiento</label>
                <input type="date" name="fecha_nac" value="{{ old('fecha_nac', optional($socio->fecha_nac)->format('Y-m-d')) }}" class="form-control">
              </div>

              <div class="col-12 form-check mt-2">
                <input type="checkbox" class="form-check-input" id="rep" name="es_representante" value="1"
                  @checked(old('es_representante', $socio->es_representante))>
                <label class="form-check-label" for="rep">Es representante</label>
              </div>
            </div>

            <div class="mt-3 d-flex gap-2">
              <button class="btn btn-primary">Actualizar</button>
              <a href="{{ route('socios.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
          </form>
        </div>
      </div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
