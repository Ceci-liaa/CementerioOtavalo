<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Subir Nuevo Archivo</strong>
                    </div>

                    @if ($errors->any())
                        <div id="error-message" class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            {{-- Mostrar la ruta de navegación --}}
                            <nav>
                                <ol class="breadcrumb p-2" style="background-color: #ffffff; border-radius: 8px;">
                                    <li class="breadcrumb-item">
                                        <a href="{{ route('files.create', ['folder_id' => null]) }}" 
                                        style="color: {{ $breadcrumb->isNotEmpty() ? '#0288d1' : '#000' }}; font-weight: bold; text-decoration: none;">
                                            🏠 Inicio
                                        </a>
                                    </li>
                                    @foreach ($breadcrumb as $crumb)
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('files.create', ['folder_id' => $crumb['id']]) }}" 
                                            style="color: #0288d1; font-weight: bold; text-decoration: none;">
                                                {{ $crumb['name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            </nav>

                            <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <!-- 🔹 Input oculto para indicar desde dónde se sube el archivo -->
                                <input type="hidden" name="from" value="{{ request('from') }}">
                                {{-- Selección de Carpeta --}}
                                <div class="mb-3 form-group">
                                    <label class="form-label">Seleccionar Carpeta:</label>
                                    <input type="hidden" name="folder_id" value="{{ $currentFolderId }}">

                                    <ul class="folder-list">
                                        @foreach ($folders as $folder)
                                            <li class="folder-item">
                                                <a href="{{ route('files.create', ['folder_id' => $folder->id, 'from' => request('from')]) }}"
                                                class="folder-link">
                                                    📁 {{ $folder->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                
                                {{-- Input: Nombre Predefinido --}}
                                <div class="mb-3 form-group">
                                    <label class="form-label">Nombre predefinido:</label>
                                    <select name="file_name_id" class="form-select" required>
                                        @foreach ($fileNames as $fileName)
                                            <option value="{{ $fileName->id }}">{{ $fileName->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="mb-3 form-group">
                                        <label class="form-label">Prefijo (opcional):</label>
                                        <input type="text" name="prefix" class="form-control" placeholder="Ej: 1.-">
                                    </div>

                                    <div class="mb-3 form-group">
                                        <label class="form-label">Sufijo (opcional):</label>
                                        <input type="text" name="suffix" class="form-control" placeholder="Ej: MLAB123">
                                    </div>
                                </div>

                                {{-- Input: Archivo --}}
                                <div class="mb-3 form-group">
                                    <label class="form-label">Archivo:</label>
                                    <input type="file" name="uploaded_file" class="form-control" required>
                                </div>

                                {{-- Botones --}}
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-success">Subir Archivo</button>
                                        <a href="{{ request('from') === 'explorer' 
                                        ? route('folders.explorer', ['id' => $currentFolderId]) 
                                        : route('files.index') }}" class="btn btn-secondary">Cancelar</a>
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
    document.addEventListener("DOMContentLoaded", function () {
        setTimeout(() => {
            const error = document.getElementById("error-message");
            if (error) {
                error.style.transition = "opacity 0.5s ease";
                error.style.opacity = 0;
                setTimeout(() => error.remove(), 500);
            }
        }, 5000);
    });
</script>

</x-app-layout>

{{-- Estilos Personalizados --}}
<style>
    /* Estilos generales */
    .form-group {
        margin-bottom: 15px;
    }

    .form-label {
        font-weight: bold;
        font-size: 14px;
    }

    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px;
        font-size: 14px;
    }

    /* Lista de Carpetas */
    .folder-list {
        list-style: none;
        padding: 0;
    }

    .folder-item {
        background-color: #ffffff;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 5px;
        font-size: 13px; /* Letra más pequeña */
        display: flex;
        align-items: center;
        transition: 0.3s;
    }

    .folder-item:hover {
        background-color: #e1f5fe; /* Celeste clarito */
        border-left: 5px solid #0288d1;
    }

    .folder-link {
        color: #000; /* Color negro por defecto */
        text-decoration: none;
        font-weight: bold;
    }

    .folder-link:hover {
        color: #0288d1; /* Celeste al pasar el cursor */
    }
</style>
