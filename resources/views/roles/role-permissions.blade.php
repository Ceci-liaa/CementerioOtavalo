<x-app-layout>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <x-app.navbar />

    <div class="px-5 py-4 container-fluid">
      <div class="row">
        <div class="col-12">

          <div class="alert alert-dark text-sm" role="alert">
            <strong style="font-size:24px;">Permisos para: {{ $role->name }}</strong>
          </div>

          <form action="{{ route('roles.permissions.update', $role) }}" method="POST" class="card">
            @csrf @method('PUT')
            <div class="card-body">

              @foreach($permissions as $grupo => $perms)
                <div class="mb-3 border rounded p-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 text-capitalize">{{ $grupo }}</h6>
                    <div class="btn-group btn-group-sm">
                      <button type="button" class="btn btn-outline-secondary js-group-check" data-group="{{ $grupo }}">Todas</button>
                      <button type="button" class="btn btn-outline-secondary js-group-uncheck" data-group="{{ $grupo }}">Ninguna</button>
                    </div>
                  </div>

                  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-2">
                    @foreach($perms as $p)
                      <div class="col">
                        <div class="form-check">
                          <input class="form-check-input perm-{{ $grupo }}"
                                 type="checkbox"
                                 id="perm-{{ $p->id }}"
                                 name="permissions[]"
                                 value="{{ $p->id }}"
                                 @checked(in_array($p->id, $rolePermissionIds))>
                          <label class="form-check-label" for="perm-{{ $p->id }}">{{ $p->name }}</label>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach

            </div>

            <div class="card-footer d-flex gap-2 justify-content-end">
              <a href="{{ route('roles.index') }}" class="btn btn-secondary">⬅️ Volver</a>
              <button class="btn btn-primary">
                <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
              </button>
            </div>
          </form>

        </div>
      </div>
    </div>

    <x-app.footer />
  </main>

  <script>
    // seleccionar/deseleccionar por grupo
    document.querySelectorAll('.js-group-check').forEach(btn => {
      btn.addEventListener('click', () => {
        const group = btn.getAttribute('data-group');
        document.querySelectorAll('.perm-' + group).forEach(cb => cb.checked = true);
      });
    });
    document.querySelectorAll('.js-group-uncheck').forEach(btn => {
      btn.addEventListener('click', () => {
        const group = btn.getAttribute('data-group');
        document.querySelectorAll('.perm-' + group).forEach(cb => cb.checked = false);
      });
    });
  </script>
</x-app-layout>
