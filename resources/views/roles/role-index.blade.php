<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Gestión de Roles</strong>
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

                    <a href="{{ route('roles.create') }}" class="btn btn-success mb-3">
                        <i class="fa-solid fa-plus"></i> Nuevo Rol
                    </a>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle text-center"> {{-- <-
                                        text-center a toda la tabla --}} <thead class="table-dark">
                                        <tr>
                                            <th style="width:70px;">ID</th>
                                            <th style="width:260px;">Rol</th>
                                            <th>Permisos</th>
                                            <th style="width:160px;">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($roles as $role)
                                                <tr>
                                                    <td>{{ $role->id }}</td>

                                                    {{-- Si quieres también el nombre centrado, deja así; si lo prefieres a
                                                    la izquierda, cambia a class="text-start" --}}
                                                    <td class="fw-semibold">{{ $role->name }}</td>

                                                    <td class="permissions-cell">
                                                        @if($role->permissions->isEmpty())
                                                            <span class="text-muted">Sin permisos</span>
                                                        @else
                                                            {{-- centrado y con wrap --}}
                                                            <div
                                                                class="d-flex justify-content-center flex-wrap gap-2 permission-badges">
                                                                @foreach($role->permissions as $perm)
                                                                    <span class="permission-pill" title="{{ $perm->name }}">
                                                                        {{ $perm->name }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </td>

                                                    <td>
                                                        <a href="{{ route('roles.edit', $role) }}"
                                                            class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="fa-solid fa-pen-to-square"
                                                                style="font-size: .85rem;"></i>
                                                        </a>
                                                        <form action="{{ route('roles.destroy', $role) }}" method="POST"
                                                            class="d-inline js-delete-form">
                                                            @csrf @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-danger js-delete"
                                                                data-item="{{ $role->name }}" title="Eliminar">
                                                                <i class="fa-solid fa-trash" style="font-size: .85rem;"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">No hay roles
                                                        registrados.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $roles->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

    {{-- ✅ Script para ocultar mensajes flash --}}
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            ['ok-msg', 'err-msg'].forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                setTimeout(() => {
                    el.style.transition = "opacity .5s";
                    el.style.opacity = 0;
                    setTimeout(() => el.remove(), 500);
                }, 5000);
            });
        });

        // ✅ Script para confirmación con SweetAlert2
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.js-delete');
            if (!btn) return;

            const form = btn.closest('form');
            if (!form) return;

            const item = btn.getAttribute('data-item') || 'este registro';

            Swal.fire({
                title: '¿Eliminar rol?',
                html: `¿Está seguro de que desea eliminar <b>"${item}"</b>?<br>Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                focusCancel: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
</x-app-layout>