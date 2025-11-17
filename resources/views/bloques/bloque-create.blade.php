<x-app-layout>
  <main class="main-content">
    <x-app.navbar />
    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Nuevo Bloque</h4>
        <a href="{{ route('bloques.index') }}" class="btn btn-secondary">Volver</a>
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
        <form method="POST" action="{{ route('bloques.store') }}">
          @csrf
          <div class="row g-3">

            {{-- Selección de polígono creado en QGIS (opcional) --}}
            <div class="col-md-6">
              <label class="form-label">Polígono (QGIS) <small class="text-muted">(opcional)</small></label>
              <select id="bloque_geom_id" name="bloque_geom_id" class="form-control">
                <option value="">-- Seleccione un polígono del mapa --</option>
                @isset($bloquesGeom)
                  @foreach($bloquesGeom as $bg)
                    <option value="{{ $bg->id }}" @if(old('bloque_geom_id') == $bg->id) selected @endif>
                      {{ $bg->id }} - {{ $bg->nombre }}
                    </option>
                  @endforeach
                @endisset
              </select>
              <div class="form-text">
                Si ya dibujaste el polígono en QGIS, selecciónalo aquí. El código se generará automáticamente.
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Nombre *</label>
              <input name="nombre" value="{{ old('nombre') }}" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Área (m²)</label>
              <input type="number" step="0.01" min="0" name="area_m2" value="{{ old('area_m2') }}" class="form-control">
              <div class="form-text">Opcional. Si lo deja vacío se calculará desde la geometría (si existe).</div>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" rows="4" class="form-control">{{ old('descripcion') }}</textarea>
            </div>

            {{-- Geometría oculta (se llena automáticamente, sin previsualización) --}}
            <input type="hidden" id="geom" name="geom" value="{{ old('geom', '') }}">

          </div>

          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary">Guardar</button>
            <a href="{{ route('bloques.index') }}" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div></div>
    </div>
    <x-app.footer />
  </main>

  {{-- SIN Leaflet / SIN mapa / SIN JSON --}}
  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('bloque_geom_id');
    const geomHidden = document.getElementById('geom');

    async function loadGeo(id) {
      if (!id) {
        if (geomHidden) geomHidden.value = '';
        return;
      }
      try {
        // Usar la ruta nombrada y reemplazar el ID
        let template = "{{ route('bloques_geom.geojson', ['id' => '__ID__']) }}";
        let url = template.replace('__ID__', encodeURIComponent(id));

        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        if (!res.ok) {
          if (geomHidden) geomHidden.value = '';
          return;
        }
        const geo = await res.json();
        if (geomHidden) geomHidden.value = JSON.stringify(geo);
      } catch (err) {
        console.error(err);
        if (geomHidden) geomHidden.value = '';
      }
    }

    if (select) {
      select.addEventListener('change', () => loadGeo(select.value));
      if (select.value) loadGeo(select.value); // cargar valor por defecto (old)
    }
  });
  </script>
</x-app-layout>
