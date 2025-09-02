<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Explorador de Carpetas</strong>
                    </div>

                    <!-- ✅ Mensaje de éxito -->
                    @if (session('success'))
                        <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <h4>📁 {{ $folder->name }}</h4>
                            <p><strong>Ubicación:</strong> {{ $folder->parent ? $folder->parent->name : 'Raíz' }}</p>

                            <a href="{{ route('folders.index') }}" class="btn btn-secondary">⬅ Volver</a>
                        </div>
                    </div>

                    <!-- ✅ Mostrar subcarpetas si existen -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5>📂 Subcarpetas</h5>
                            @if ($folder->subfolders->isNotEmpty())
                                <ul class="list-group">
                                    @foreach ($folder->subfolders as $subfolder)
                                        <li class="list-group-item">
                                            <a href="{{ route('folders.show', $subfolder) }}">📁 {{ $subfolder->name }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No hay subcarpetas en esta carpeta.</p>
                            @endif
                        </div>
                    </div>

                    <!-- ✅ Tabla para mostrar archivos SIN acciones -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5>📄 Archivos</h5>
                            @if ($folder->files->isNotEmpty())
                                <div class="table-responsive">
                                    <table class="table table-hover table-bordered text-center">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th> <!-- Desde file_names -->
                                                <th>Usuario</th>
                                                <th>Fecha Subida</th>
                                                <th>Fecha Modificación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($folder->files as $file)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        {{ ($file->prefix ? $file->prefix . ' ' : '') .
                                                        ($file->file_name->name ?? '') .
                                                        ($file->suffix ? ' ' . $file->suffix : '') }}
                                                    </td>
                                                    <td>{{ $file->user->name }}</td>
                                                    <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>{{ $file->updated_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted">No hay archivos en esta carpeta.</p>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

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
    </script>
</x-app-layout>
