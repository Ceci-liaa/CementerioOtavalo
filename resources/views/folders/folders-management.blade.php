<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Gestión de Carpetas</strong>
                    </div>

                    <!-- ✅ Mensaje de éxito solo cuando se actualiza una carpeta -->
                    @if (session('success'))
                        <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <a href="{{ route('folders.create') }}" class="btn btn-success mb-3">Nueva Carpeta</a>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive"> <!-- Agregado para hacer la tabla responsive -->
                                <table class="table table-hover table-bordered text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th>
                                            <th>Ubicación</th>
                                            <th>Creado por</th>
                                            <th>Fecha Creación</th>
                                            <th>Fecha Modificación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($folders as $folder)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a href="{{ route('folders.show', $folder) }}">
                                                        📁 {{ $folder->name }}
                                                    </a>
                                                </td>
                                                <td><strong>{{ $folder->full_path }}</strong></td> <!-- Nueva columna: ruta completa -->
                                                <td>{{ $folder->user->name }}</td>
                                                <td>{{ $folder->created_at }}</td>
                                                <td>{{ $folder->updated_at }}</td>
                                                <td>
                                                    <a href="{{ route('folders.edit', $folder) }}" class="btn btn-sm btn-warning"> <i class="fa-solid fa-pen-to-square" style="font-size: 0.75rem;"></i> <!-- Icono de editar --> </a>
                                                    <!-- Botón de eliminar -->
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('{{ $folder->id }}', '{{ $folder->name }}')">
                                                        <i class="fa-solid fa-trash" style="font-size: 0.75rem;"></i> <!-- Icono de eliminar -->  
                                                        </button>

                                                        <!-- Formulario oculto para la eliminación -->
                                                        <form id="delete-form-{{ $folder->id }}" action="{{ route('folders.destroy', $folder->id) }}" method="POST" style="display: none;">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

    <!-- ✅ Script para ocultar el mensaje después de 5 segundos -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(function () {
                let successMessage = document.getElementById('success-message');
                if (successMessage) {
                    successMessage.style.transition = "opacity 0.5s";
                    successMessage.style.opacity = 0;
                    setTimeout(() => successMessage.remove(), 500);
                }
            }, 5000);
        });

        function confirmDelete(folderId, folderName) {
            Swal.fire({
                title: "¿Eliminar carpeta?",
                text: `¿Está seguro de que desea eliminar la carpeta "${folderName}"? Esta acción no se puede deshacer.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${folderId}`).submit();
                }
            });
        }
    </script>
</x-app-layout>
