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
              <div class="form-text">Si ya dibujaste el polígono en QGIS, selecciónalo aquí. El código se generará automáticamente.</div>
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

            {{-- Hidden geom (opcional) y preview --}}
            <input type="hidden" id="geom" name="geom" value="{{ old('geom', '') }}">

            <div class="col-12">
              <label class="form-label d-flex justify-content-between">
                <span>Previsualización del polígono</span>
              </label>
              <div id="mini-map" style="height:300px;border:1px solid #e1e1e1;"></div>
              <pre id="geo-preview" class="bg-light p-2 mt-2" style="max-height:200px; overflow:auto; white-space:pre-wrap;">@if(old('geom')){{ old('geom') }}@endif</pre>
              <div class="form-text">El sistema copia la geometría desde la capa seleccionada en QGIS. No es necesario pegar GeoJSON manualmente.</div>
            </div>

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

  {{-- Leaflet (CDN). Si necesitas offline, descarga estos archivos a public/vendor/leaflet y cambia las rutas. --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('bloque_geom_id');
    const preview = document.getElementById('geo-preview');
    const geomHidden = document.getElementById('geom');

    // Inicializar mini-mapa (sin tiles por defecto — funciona offline).
    const map = L.map('mini-map', { center: [0,0], zoom: 2, attributionControl: false });
    // Si quieres tiles (mejor), descomenta la siguiente línea (requiere internet):
    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    let geoLayer = null;

    async function loadGeo(id) {
      if (!id) {
        if (geoLayer) { geoLayer.remove(); geoLayer = null; }
        preview.textContent = '';
        if (geomHidden) geomHidden.value = '';
        return;
      }

      preview.textContent = 'Cargando geometría...';
      try {
        // usar la ruta nombrada: generamos template y reemplazamos __ID__
        let template = "{{ route('bloques_geom.geojson', ['id' => '__ID__']) }}";
        let url = template.replace('__ID__', encodeURIComponent(id));

        const res = await fetch(url, { headers: {'Accept':'application/json'} });
        if (!res.ok) {
          preview.textContent = 'GeoJSON no disponible';
          if (geomHidden) geomHidden.value = '';
          return;
        }
        const geo = await res.json();
        const pretty = JSON.stringify(geo, null, 2);
        preview.textContent = pretty;
        if (geomHidden) geomHidden.value = pretty;

        if (geoLayer) geoLayer.remove();
        geoLayer = L.geoJSON(geo).addTo(map);
        map.fitBounds(geoLayer.getBounds(), { padding: [10,10] });
      } catch (err) {
        console.error(err);
        preview.textContent = 'Error al cargar geometría';
        if (geomHidden) geomHidden.value = '';
      }
    }

    if (select) {
      select.addEventListener('change', () => loadGeo(select.value));
      // si hay valor por defecto (old), cargarlo
      if (select.value) loadGeo(select.value);
    }
  });
  </script>
</x-app-layout>
