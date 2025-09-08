<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="alert alert-dark text-sm mb-0" role="alert">
                            <strong style="font-size: 24px;">Administrador de permisos</strong>
                            <div class="text-muted">Asigna permisos a cada rol</div>
                        </div>
                    </div>

                    @if(session('ok'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert" id="ok-msg">
                            {{ session('ok') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert" id="err-msg">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- Buscador --}}
                    <div class="mb-2">
                        <input type="text" id="perm-search" class="form-control" placeholder="Presiona “/” para buscar permisos…">
                    </div>

                    <div class="card">
                        <div class="card-body p-0">
                            <form id="matrix-form" action="{{ route('roles.permissions.manager.update') }}" method="POST">
                                @csrf
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered align-middle text-center mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th class="text-start" style="min-width: 280px;">Permisos</th>

                                                @foreach($roles as $role)
                                                    <th style="min-width: 160px;">
                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                            <span class="fw-semibold">
                                                                {{ $role->name }}
                                                                @if($role->name === 'Administrador')
                                                                    <span class="badge bg-secondary ms-1" title="Columna bloqueada">
                                                                        <i class="fa-solid fa-lock"></i>
                                                                    </span>
                                                                @endif
                                                            </span>

                                                            @if($role->name !== 'Administrador')
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <button class="btn btn-outline-secondary col-check-all" data-role="{{ $role->id }}" type="button">Todas</button>
                                                                    <button class="btn btn-outline-secondary col-uncheck-all" data-role="{{ $role->id }}" type="button">Ninguna</button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </th>
                                                @endforeach
                                            </tr>
                                        </thead>

                                        <tbody id="perm-tbody">
                                            @foreach($permissions as $perm)
                                                <tr class="perm-row">
                                                    <td class="text-start perm-name" data-name="{{ strtolower($perm->name) }}">
                                                        {{ $perm->name }}
                                                    </td>

                                                    @foreach($roles as $role)
                                                        <td>
                                                            <input type="checkbox"
                                                                   class="form-check-input perm-cell"
                                                                   name="permission_role[{{ $perm->id }}][{{ $role->id }}]"
                                                                   @checked(in_array($perm->id, $rolePerms[$role->id] ?? []))
                                                                   @disabled($role->name === 'Administrador')>
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="p-3 border-top text-end">
                                    <button class="btn btn-primary">
                                        <i class="fa-solid fa-floppy-disk"></i> Guardar cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

    <script>
        // Foco rápido con "/"
        document.addEventListener('keydown', e => {
            if (e.key === '/') {
                e.preventDefault();
                document.getElementById('perm-search').focus();
            }
        });

        // Buscar permisos
        const search = document.getElementById('perm-search');
        search.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            document.querySelectorAll('#perm-tbody .perm-row').forEach(row => {
                const name = row.querySelector('.perm-name').dataset.name;
                row.style.display = name.includes(q) ? '' : 'none';
            });
        });

        // Seleccionar/limpiar toda la columna de un rol (solo roles no bloqueados)
        document.querySelectorAll('.col-check-all').forEach(btn => {
            btn.addEventListener('click', function () {
                const roleId = this.getAttribute('data-role');
                document.querySelectorAll(`input[name^="permission_role["][name$="[${roleId}]"]`).forEach(cb => {
                    if (!cb.disabled) cb.checked = true;
                });
            });
        });
        document.querySelectorAll('.col-uncheck-all').forEach(btn => {
            btn.addEventListener('click', function () {
                const roleId = this.getAttribute('data-role');
                document.querySelectorAll(`input[name^="permission_role["][name$="[${roleId}]"]`).forEach(cb => {
                    if (!cb.disabled) cb.checked = false;
                });
            });
        });

        // Ocultar mensajes flash
        (function(){
            ['ok-msg','err-msg'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                setTimeout(() => {
                    el.style.transition = "opacity .5s";
                    el.style.opacity = 0;
                    setTimeout(() => el.remove(), 500);
                }, 4000);
            });
        })();
    </script>
</x-app-layout>
