<x-app-layout>
  <main class="main-content">
    <x-app.navbar />
    <div class="container py-4">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="m-0">Editar Bloque</h4>
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
        <form method="POST" action="{{ route('bloques.update', $bloque) }}">
          @csrf @method('PUT')
          <div class="row g-3">

            <div class="col-md-4">
              <label class="form-label">Código *</label>
              <input name="codigo" value="{{ old('codigo', $bloque->codigo) }}" class="form-control" required>
            </div>

            <div class="col-md-8">
              <label class="form-label">Nombre *</label>
              <input name="nombre" value="{{ old('nombre', $bloque->nombre) }}" class="form-control" required>
            </div>

            <div class="col-md-4">
              <label class="form-label">Área (m²)</label>
              <input type="number" step="0.01" min="0" name="area_m2" value="{{ old('area_m2', $bloque->area_m2) }}" class="form-control">
            </div>

            {{-- Selección de polígono (incluye el actualmente asignado) --}}
            <div class="col-md-8">
              <label class="form-label">Polígono (QGIS) <small class="text-muted">(opcional)</small></label>
              <select id="bloque_geom_id" name="bloque_geom_id" class="form-control">
                <option value="">-- Seleccione (o conservar actual) --</option>
                @isset($bloquesGeom)
                  @foreach($bloquesGeom as $bg)
                    <option value="{{ $bg->id }}" @if(old('bloque_geom_id', $bloque->bloque_geom_id) == $bg->id) selected @endif>
                      {{ $bg->id }} - {{ $bg->nombre }}
                    </option>
                  @endforeach
                @endisset
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea name="descripcion" rows="4" class="form-control">{{ old('descripcion', $bloque->descripcion) }}</textarea>
            </div>

            {{-- Hidden geom y preview --}}
            <input type="hidden" id="geom" name="geom" value="{{ old('geom', $bloque->geom ? json_encode($bloque->geom) : '') }}">

            <div class="col-12">
              <label class="form-label d-flex justify-content-between">
                <span>Previsualización del polígono <small class="text-muted">(si hay)</small></span>
              </label>
              <div id="mini-map" style="height:300px;border:1px solid #e1e1e1;"></div>
              <pre id="geo-preview" class="bg-light p-2 mt-2" style="max-height:200px; overflow:auto; white-space:pre-wrap;">
                @if(old('geom')){{ old('geom') }}@elseif(isset($bloque) && $bloque->geom){{ json_encode($bloque->geom) }}@endif
              </pre>
              <div class="form-text">Si pega GeoJSON se sobreescribirá la geometría; si selecciona Polígono (QGIS) la copiará desde `bloques_geom`.</div>
            </div>

          </div>

          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-primary">Actualizar</button>
            <a href="{{ route('bloques.index') }}" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div></div>
    </div>
    <x-app.footer />
  </main>

  {{-- Leaflet --}}
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <script>
  document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('bloque_geom_id');
    const preview = document.getElementById('geo-preview');
    const geomHidden = document.getElementById('geom');

    const map = L.map('mini-map', { center: [0,0], zoom: 2, attributionControl: false });
    // L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

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
        let template = "{{ route('bloques_geom.geojson', ['id' => '__ID__']) }}";
        let url = template.replace('__ID__', encodeURIComponent(id));

        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
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
      // si hay valor actual, cargarlo:
      const current = select.value || "{{ old('bloque_geom_id', $bloque->bloque_geom_id ?? '') }}";
      if (current) loadGeo(current);
    } else {
      // si no hay select (edge case), pero existe la geom en el bloque, mostrarla:
      const existing = "{{ $bloque->bloque_geom_id ?? '' }}";
      if (existing) loadGeo(existing);
    }
  });
  </script>
</x-app-layout>
