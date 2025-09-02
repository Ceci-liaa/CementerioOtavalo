<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Gestión de Archivos</strong>
                    </div>

                    <!-- ✅ Mensaje de éxito -->
                    @if (session('success'))
                        <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <a href="{{ route('files.create', ['from' => 'index']) }}" class="btn btn-success mb-3">Subir nuevo
                        archivo</a>

                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Nombre</th> <!-- Toma el valor desde la tabla file_names -->
                                            <th>Ubicación</th> <!-- Nueva columna -->
                                            <th>Usuario</th>
                                            <th>Fecha Subida</th>
                                            <th>Fecha Modificación</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($files as $file)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <!-- <td>
                                                                <a href="{{ route('files.preview', $file->id) }}" class="text-decoration-none fw-bold">
                                                                    📄 {{ $file->file_name?->name ?? 'Sin nombre' }}
                                                                </a>
                                                            </td> -->
                                                <td>
                                                    <a href="{{ route('files.preview', $file->id) }}"
                                                        class="text-decoration-none fw-bold">
                                                        📄 {{ $file->nombre_completo ?? 'Sin nombre' }}
                                                    </a>
                                                </td>
                                                <td><strong>{{ $file->full_path }}</strong></td>
                                                <td>{{ $file->user->name }}</td>
                                                <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                                <!-- Formato de fecha -->
                                                <td>{{ $file->updated_at->format('d/m/Y H:i') }}</td>
                                                <!-- Formato de fecha -->
                                                <td>
                                                    <a href="{{ route('files.show', ['file' => $file, 'from' => 'index']) }}"
                                                        class="btn btn-sm btn-info"><i class="fa-solid fa-eye"
                                                            style="font-size: 0.75rem;"></i> </a> <!-- Botón de ver -->
                                                    <a href="{{ route('files.edit', ['file' => $file, 'from' => 'index']) }}"
                                                        class="btn btn-sm btn-warning"> <i class="fa-solid fa-pen-to-square"
                                                            style="font-size: 0.75rem;"></i> <!-- Icono de editar --> </a>
                                                    <!-- Botón de eliminar -->
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="confirmDelete('{{ $file->id }}', '{{ $file->nombre_completo }}')">
                                                        <i class="fa-solid fa-trash" style="font-size: 0.75rem;"></i>
                                                    </button>
                                                    <!-- Formulario oculto para eliminar -->
                                                    <form id="delete-form-{{ $file->id }}"
                                                        action="{{ route('files.destroy', $file->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="from" value="{{ request('from') }}">
                                                        <!-- Mantiene la ubicación -->
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- ✅ Paginación mejorada -->
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $files->links() }} <!-- Genera la paginación automáticamente -->
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

        function confirmDelete(fileId, fileName) {
            Swal.fire({
                title: "¿Eliminar archivo?",
                text: `¿Está seguro de que desea eliminar el archivo "${fileName}"? Esta acción no se puede deshacer.`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${fileId}`).submit();
                }
            });
        }   
    </script>
</x-app-layout>