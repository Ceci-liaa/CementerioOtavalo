<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    @if (session('success'))
                        <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div id="error-message" class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="alert alert-dark text-sm d-flex align-items-center justify-content-between"
                        role="alert">
                        <strong style="font-size: 24px;">📂 Explorador de Archivos</strong>

                        <!-- 🔹 Breadcrumb de navegación -->
                        <span class="breadcrumb-container">
                            <strong>Ubicación:</strong>
                            @if ($folder)
                                <a href="{{ route('folders.explorer') }}">Inicio</a>
                                @foreach ($folder->getAncestors() as $ancestor)
                                    ➤ <a href="{{ route('folders.explorer', $ancestor->id) }}">{{ $ancestor->name }}</a>
                                @endforeach
                                ➤ <span class="current-folder">{{ $folder->name }}</span>
                            @else
                                <span class="current-folder">Inicio</span>
                            @endif
                        </span>
                    </div>

                    <!-- Botón para volver atrás y subir archivo -->
                    <div class="d-flex justify-content-between mb-3">
                        <div>
                            @if ($folder && $folder->parent_id)
                                <a href="{{ route('folders.explorer', $folder->parent_id) }}" class="btn btn-secondary">⬅
                                    Volver</a>
                            @else
                                <a href="{{ route('folders.explorer') }}" class="btn btn-secondary">🏠 Volver a Inicio</a>
                            @endif
                        </div>
                        <form method="GET" action="{{ route('folders.explorer', $folder ? $folder->id : null) }}"
                            class="flex-grow-1 mx-2" style="max-width: 500px;">
                            <div class="modern-search">
                                <span class="search-icon"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" value="{{ request('search') }}" class="modern-input"
                                    placeholder="Buscar archivos o carpetas...">
                                @if(request('search'))
                                    <a href="{{ route('folders.explorer', $folder ? $folder->id : null) }}"
                                        class="clear-btn"><i class="fas fa-times"></i></a>
                                @endif
                            </div>
                        </form>
                        <div>
                            <a href="{{ route('files.create', ['folder_id' => $folder ? $folder->id : null, 'from' => 'explorer']) }}"
                                class="btn btn-success mb-3">📤 Subir Archivo</a>
                        </div>
                    </div>

                    <!-- 📁 Carpetas dentro de la carpeta actual -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-3 text-start">📁 Carpetas</h5>

                            @if ($subfolders->isEmpty())
                                <p class="text-start">📂 No hay subcarpetas.</p>
                            @else
                                <div class="table-responsive" style="max-height: 300px; overflow: auto;">
                                    <table class="table table-hover table-bordered align-middle">
                                        <thead class="table-warning text-start">
                                            <tr>
                                                <th>📁 Nombre</th>
                                                <th>Usuario</th>
                                                <th>Fecha de Creación</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-start">
                                            @foreach ($subfolders as $subfolder)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('folders.explorer', ['id' => $subfolder->id]) }}"
                                                            class="text-decoration-none fw-bold">
                                                            📂 {{ $subfolder->name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $subfolder->user->name ?? 'Desconocido' }}</td>
                                                    <td>{{ $subfolder->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- 📄 Archivos dentro de la carpeta actual -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-3 text-start">📄 Archivos</h5>

                            @if ($files->isEmpty())
                                <p class="text-start">📂 No hay archivos en esta carpeta.</p>
                            @else
                                <div class="table-responsive" style="max-height: 350px; overflow: auto;">
                                    <table class="table table-hover table-bordered align-middle">
                                        <thead class="table-dark text-start">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nombre</th>
                                                <th>Usuario</th>
                                                <th>Fecha Subida</th>
                                                <th>Fecha Modificación</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-start">
                                            @foreach ($files as $file)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>
                                                        <a href="{{ route('files.preview', $file->id) }}"
                                                            class="text-decoration-none fw-bold">
                                                            📄 {{ $file->nombre_completo }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $file->user->name }}</td>
                                                    <td>{{ $file->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>{{ $file->updated_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <a href="{{ route('files.show', ['file' => $file, 'from' => 'explorer']) }}"
                                                            class="btn btn-sm btn-info">
                                                            <i class="fa-solid fa-eye" style="font-size: 0.75rem;"></i>
                                                        </a>
                                                        <a href="{{ route('files.edit', ['file' => $file, 'from' => 'explorer']) }}"
                                                            class="btn btn-sm btn-warning">
                                                            <i class="fa-solid fa-pen-to-square"
                                                                style="font-size: 0.75rem;"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-danger"
                                                            onclick="confirmDelete('{{ $file->id }}', '{{ $file->nombre_completo }}', '{{ $file->prefix }}', '{{ $file->suffix }}')">
                                                            <i class="fa-solid fa-trash" style="font-size: 0.75rem;"></i>
                                                        </button>

                                                        <form id="delete-form-{{ $file->id }}"
                                                            action="{{ route('files.destroy', $file->id) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="from" value="explorer">
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

    <!-- ✅ Estilos personalizados -->
    <style>
        .breadcrumb-container {
            font-size: 11px;
        }

        .breadcrumb-container a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }

        .breadcrumb-container a:hover {
            text-decoration: underline;
        }

        .current-folder {
            font-weight: bold;
            color: #333;
        }

        .folder-card {
            background-color: #fff9c4;
            border: 1px solid #ffeb3b;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .folder-card:hover {
            background-color: #ffe082;
            transform: scale(1.05);
        }

        .folder-icon {
            color: #fbc02d;
        }

        .folder-name {
            font-weight: bold;
            color: #795548;
        }
    </style>

    <style>
        .modern-search {
            position: relative;
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 2rem;
            background-color: #fff;
            padding: 0.5rem 1rem;
            height: 48px;
            transition: all 0.3s ease;
        }

        .modern-search:focus-within {
            box-shadow: 0 0 0 2px #007bff40;
            border-color: #007bff;
        }

        .modern-input {
            border: none;
            outline: none;
            width: 100%;
            padding-left: 1.5rem;
            font-size: 1rem;
            background-color: transparent;
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            color: #999;
        }

        .clear-btn {
            color: #999;
            text-decoration: none;
            font-size: 1.2rem;
            margin-left: 0.5rem;
        }

        .clear-btn:hover {
            color: #333;
        }
    </style>

    <style>
        /* Borde redondeado del cuadro de sugerencias */
        .ui-autocomplete {
            border-radius: 12px !important;
            border: 1px solid #ccc;
            background-color: white;
            z-index: 9999 !important;
            padding: 0;
            overflow: hidden;
            /* 👈 Evita bordes cuadrados al hacer hover */
        }

        /* Ítems del menú */
        .ui-menu-item-wrapper {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
            color: #333;
            /* texto normal */
        }

        /* Al pasar el mouse: fondo celeste, texto negro y mantiene borde redondeado */
        .ui-menu-item-wrapper:hover,
        .ui-state-active {
            background-color: rgb(158, 232, 255) !important;
            color: #000 !important;
            border-radius: 0 !important;
            /* Evita esquinas cuadradas individuales */
        }
    </style>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const success = document.getElementById('success-message');
            const error = document.getElementById('error-message');

            if (success) {
                setTimeout(() => {
                    success.style.transition = 'opacity 1s';
                    success.style.opacity = 0;
                    setTimeout(() => success.remove(), 1000);
                }, 5000);
            }

            if (error) {
                setTimeout(() => {
                    error.style.transition = 'opacity 1s';
                    error.style.opacity = 0;
                    setTimeout(() => error.remove(), 1000);
                }, 5000);
            }
        });
    </script>
    <script>
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



    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <!-- <script>
$(function() {
    $('input[name="search"]').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "{{ route('folders.suggestions') }}",
                data: { term: request.term },
                success: function(data) {
                    response(data);
                }
            });
        },
        minLength: 1, // ✅ Ahora permite búsquedas desde 1 carácter
        select: function(event, ui) {
            if (ui.item.url) {
                window.location.href = ui.item.url;
            }
        }
    });
});
</script> -->
    <script>
        $(function () {
            $('input[name="search"]').autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: "{{ route('folders.suggestions') }}",
                        data: { term: request.term },
                        success: function (data) {
                            console.log("📦 Resultados recibidos:", data); // ← Aquí verás en la consola qué llega desde el backend
                            response(data);
                        },
                        error: function (xhr, status, error) {
                            console.error("❌ Error en la búsqueda:", error); // ← También logea si hay error
                        }
                    });
                },
                minLength: 1,
                select: function (event, ui) {
                    if (ui.item.url) {
                        window.location.href = ui.item.url;
                    }
                }
            });
        });
    </script>
</x-app-layout>