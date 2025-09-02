<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Editar Archivo: {{ $file->name_original }}</strong>
                    </div>

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

                    <div class="card">
                        <div class="card-body">
                            {{-- Mostrar la ruta de navegación --}}
                            <nav>
                                <ol class="breadcrumb p-2" id="breadcrumb" style="background-color: #ffffff; border-radius: 8px;">
                                    <li class="breadcrumb-item">
                                        <a href="#" onclick="navigateTo(null)" style="color: #0288d1; font-weight: bold; text-decoration: none;">
                                            🏠 Inicio
                                        </a>
                                    </li>
                                </ol>
                            </nav>

                            <form action="{{ route('files.update', $file) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <input type="hidden" name="from" value="{{ request('from', 'index') }}">
                                <input type="hidden" name="folder_id" id="selected-folder" value="{{ $file->folder_id }}">

                                {{-- Input: Nombre Predefinido --}}
                                <div class="mb-3 form-group">
                                    <label class="form-label">Nombre predefinido:</label>
                                    <select name="file_name_id" class="form-select" required>
                                        @foreach ($fileNames as $fileName)
                                            <option value="{{ $fileName->id }}" {{ $file->file_name_id == $fileName->id ? 'selected' : '' }}>
                                                {{ $fileName->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mb-3 form-group">
                                        <label class="form-label">Prefijo (opcional):</label>
                                        <input type="text" name="prefix" class="form-control" value="{{ old('prefix', $file->prefix ?? '') }}" placeholder="Ej: 1.-">
                                    </div>

                                    <div class="mb-3 form-group">
                                        <label class="form-label">Sufijo (opcional):</label>
                                        <input type="text" name="suffix" class="form-control" value="{{ old('suffix', $file->suffix ?? '') }}" placeholder="Ej: MLAB123">
                                    </div>
                                </div>

                                {{-- Ubicación Actual --}}
                                <div class="mb-3 form-group">
                                    <label class="form-label">Ubicación Actual:</label>
                                    <div class="alert alert-info" id="current-location">{{ $file->folder ? $file->folder->full_path : '🏠 Inicio' }}</div>
                                </div>

                                {{-- Nueva Ubicación --}}
                                <div class="mb-3 form-group">
                                    <label class="form-label">Nueva Ubicación:</label>
                                    <ul class="folder-list" id="folder-container">
                                        @foreach ($parentFolders as $folder)
                                            <li class="folder-item">
                                                <a href="#" onclick="navigateTo('{{ $folder->id }}')" class="folder-link">
                                                    📁 {{ $folder->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                {{-- Botones --}}
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">Actualizar Archivo</button>

                                    @php
                                        $from = request('from', 'index');
                                        $cancelUrl = $from === 'explorer'
                                            ? route('folders.explorer', ['id' => $file->folder_id])
                                            : route('files.index');
                                    @endphp

                                    <a href="{{ $cancelUrl }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

    {{-- 🔹 Incluir jQuery si no está incluido en el layout --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- 🔹 Script para ocultar automáticamente los mensajes --}}
<script>
    $(document).ready(function () {
        setTimeout(function () {
            $('#success-message').fadeOut('slow', function () {
                $(this).remove();
            });
            $('#error-message').fadeOut('slow', function () {
                $(this).remove();
            });
        }, 5000); // ⏱️ Tiempo en milisegundos (5s)
    });
</script>


</x-app-layout>

<script>
    function navigateTo(folderId) {
        document.getElementById('selected-folder').value = folderId || '';
        updateBreadcrumb(folderId);
        updateFolderList(folderId);
    }

    function updateBreadcrumb(folderId) {
        let breadcrumb = document.getElementById('breadcrumb');
        breadcrumb.innerHTML = '<li class="breadcrumb-item"><a href="#" onclick="navigateTo(null)" style="color: #0288d1; font-weight: bold; text-decoration: none;">🏠 Inicio</a></li>';

        let currentFolder = folderId ? folders.find(f => f.id == folderId) : null;
        let path = [];

        while (currentFolder) {
            path.unshift(`<li class="breadcrumb-item"><a href="#" onclick="navigateTo('${currentFolder.id}')" style="color: #0288d1; font-weight: bold; text-decoration: none;">${currentFolder.name}</a></li>`);
            currentFolder = folders.find(f => f.id == currentFolder.parent_id);
        }

        breadcrumb.innerHTML += path.join('');
    }

    function updateFolderList(folderId) {
        let folderContainer = document.getElementById('folder-container');
        folderContainer.innerHTML = '';

        let subfolders = folders.filter(f => f.parent_id == folderId);
        subfolders.forEach(folder => {
            folderContainer.innerHTML += `<li class="folder-item"><a href="#" onclick="navigateTo('${folder.id}')" class="folder-link">📁 ${folder.name}</a></li>`;
        });
    }

    let folders = @json($allFolders);
</script>

<style>
    .form-group { margin-bottom: 15px; }
    .form-label { font-weight: bold; font-size: 14px; }
    .form-control, .form-select { border-radius: 8px; padding: 10px; font-size: 14px; }
    .folder-list { list-style: none; padding: 0; }
    .folder-item { background-color: #ffffff; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 5px; font-size: 13px; display: flex; align-items: center; transition: 0.3s; }
    .folder-item:hover { background-color: #e1f5fe; border-left: 5px solid #0288d1; }
    .folder-link { color: #000; text-decoration: none; font-weight: bold; }
    .folder-link:hover { color: #0288d1; }
</style>
