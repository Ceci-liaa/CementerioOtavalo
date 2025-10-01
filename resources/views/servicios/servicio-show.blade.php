<x-app-layout>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <x-app.navbar />

    <div class="px-5 py-4 container-fluid">
      <div class="row"><div class="col-lg-8 mx-auto">

        <div class="alert alert-dark text-sm"><strong style="font-size:24px;">Detalle del Servicio</strong></div>

        <div class="card"><div class="card-body">
          <p><b>ID:</b> {{ $servicio->id }}</p>
          <p><b>Nombre:</b> {{ $servicio->nombre }}</p>
          <p><b>Valor:</b> {{ is_null($servicio->valor) ? '—' : number_format($servicio->valor,2) }}</p>
          <p><b>Descripción:</b><br>{{ $servicio->descripcion ?? '—' }}</p>
        </div></div>

        <div class="mt-3 d-flex gap-2">
          <a href="{{ route('servicios.edit',$servicio) }}" class="btn btn-warning">Editar</a>
          <a href="{{ route('servicios.index') }}" class="btn btn-secondary">Volver</a>
        </div>

      </div></div>
    </div>

    <x-app.footer />
  </main>
</x-app-layout>
