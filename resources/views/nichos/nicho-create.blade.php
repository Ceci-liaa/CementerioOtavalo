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
            {{-- Datos del nicho --}}
            <div class="col-md-4">
              <label class="form-label">Bloque *</label>
              <select name="bloque_id" class="form-control" required>
                <option value="">-- Seleccione --</option>
                @foreach($bloques as $b)
                  <option value="{{ $b->id }}" @selected(old('bloque_id')==$b->id)>
                    {{ $b->nombre ?? ('Bloque #'.$b->id) }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Código del nicho *</label>
              <input name="codigo" value="{{ old('codigo') }}" class="form-control" required>
              <small class="text-muted">Ej: 12-A, N-203, etc.</small>
            </div>

            <div class="col-md-2">
              <label class="form-label">Capacidad *</label>
              <input type="number" name="capacidad" min="1" step="1" value="{{ old('capacidad',1) }}" class="form-control" required>
            </div>

            <div class="col-md-2">
              <label class="form-label">Estado *</label>
              <select name="estado" class="form-control" required>
                @foreach(['disponible','ocupado','mantenimiento'] as $e)
                  <option value="{{ $e }}" @selected(old('estado','disponible')==$e)>{{ ucfirst($e) }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" rows="2" class="form-control">{{ old('descripcion') }}</textarea>
            </div>

            <hr class="mt-2">

            {{-- Fallecido (opcional) --}}
            <div class="col-12"><h6>Fallecido (opcional)</h6></div>
            <div class="col-md-6">
              <label class="form-label">Seleccionar fallecido</label>
              <select name="fallecido[id]" class="form-control">
                <option value="">-- Ninguno --</option>
                @foreach($fallecidos as $f)
                  @php $doc = $f->cedula ?? 's/d'; @endphp
                  <option value="{{ $f->id }}" @selected(old('fallecido.id')==$f->id)>
                    {{ $f->apellidos }} {{ $f->nombres }} ({{ $doc }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">Posición</label>
              <input type="number" name="fallecido[posicion]" min="1" step="1" value="{{ old('fallecido.posicion',1) }}" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Inhumación</label>
              <input type="date" name="fallecido[fecha_inhumacion]" value="{{ old('fallecido.fecha_inhumacion') }}" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">Exhumación</label>
              <input type="date" name="fallecido[fecha_exhumacion]" value="{{ old('fallecido.fecha_exhumacion') }}" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Observación</label>
              <textarea name="fallecido[observacion]" rows="2" class="form-control">{{ old('fallecido.observacion') }}</textarea>
            </div>

            <hr class="mt-2">

            {{-- Responsable (opcional) --}}
            <div class="col-12"><h6>Responsable / Titular (opcional)</h6></div>
            <div class="col-md-6">
              <label class="form-label">Seleccionar socio</label>
              <select name="responsable[socio_id]" class="form-control">
                <option value="">-- Ninguno --</option>
                @foreach($socios as $s)
                  @php $doc = $s->cedula ?? 's/d'; @endphp
                  <option value="{{ $s->id }}" @selected(old('responsable.socio_id')==$s->id)>
                    {{ $s->apellidos }} {{ $s->nombres }} ({{ $doc }})
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Rol</label>
              <select name="responsable[rol]" class="form-control">
                @foreach(['TITULAR','CO-TITULAR','RESPONSABLE'] as $rol)
                  <option value="{{ $rol }}" @selected(old('responsable.rol','TITULAR')==$rol)>{{ $rol }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label d-block">Vigencia</label>
              <div class="d-flex gap-2">
                <input type="date" name="responsable[desde]" value="{{ old('responsable.desde') }}" class="form-control">
                <input type="date" name="responsable[hasta]" value="{{ old('responsable.hasta') }}" class="form-control">
              </div>
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
