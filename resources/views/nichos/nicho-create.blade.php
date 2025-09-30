<x-app-layout>
  <main class="main-content">
    <x-app.navbar />

    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Nuevo Nicho</h4>
        <a href="{{ route('nichos.index') }}" class="btn btn-secondary">Volver</a>
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
        <form method="POST" action="{{ route('nichos.store') }}">
          @csrf

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Bloque *</label>
              <select name="bloque_id" class="form-select" required>
                <option value="">— Seleccione —</option>
                @foreach($bloques as $b)
                  <option value="{{ $b->id }}" @selected(old('bloque_id')==$b->id)>
                    {{ $b->codigo }} — {{ $b->nombre }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Código *</label>
              <input name="codigo" value="{{ old('codigo') }}" class="form-control" required>
              <small class="text-muted">Único por bloque.</small>
            </div>

            <div class="col-md-4">
              <label class="form-label">Capacidad *</label>
              <input type="number" min="1" step="1" name="capacidad" value="{{ old('capacidad',1) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Estado *</label>
              <select name="estado" class="form-select" required>
                @foreach(['disponible'=>'Disponible','ocupado'=>'Ocupado','mantenimiento'=>'Mantenimiento'] as $k=>$v)
                  <option value="{{ $k }}" @selected(old('estado','disponible')==$k)>{{ $v }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">QR UUID (opcional)</label>
              <input name="qr_uuid" value="{{ old('qr_uuid') }}" class="form-control" placeholder="UUID v4">
            </div>

            <div class="col-12">
              <label class="form-label d-flex justify-content-between">
                <span>Geom (JSON) <small class="text-muted">(opcional)</small></span>
              </label>
              <textarea name="geom" rows="3" class="form-control" placeholder='{"type":"Polygon","coordinates":[...]}'>{{ old('geom') }}</textarea>
            </div>
          </div>

          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary">Guardar</button>
            <a href="{{ route('nichos.index') }}" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div></div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
