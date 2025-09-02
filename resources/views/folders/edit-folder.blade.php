<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Editar Carpeta</strong>
                    </div>

                    <div class="card">

                        <!-- ✅ Mensajes de error y éxito -->
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show fade-message" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="card-body">
                            <form action="{{ route('folders.update', $folder->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Nombre de la Carpeta</label>
                                        <input type="text" name="name" class="form-control" value="{{ $folder->name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Nueva ubicación</label>
                                        <div class="border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                            <input type="hidden" name="parent_id" id="parent_id" value="{{ $folder->parent_id }}">
                                            <div><strong>Ruta actual:</strong> {{ $folder->getFullPathAttribute() }}</div>
                                            <div><strong>Nueva ubicación:</strong> <span id="breadcrumb">Inicio</span></div>
                                            <ul id="folder-list" class="list-unstyled mt-2">
                                                <li>
                                                    <div onclick="selectAsRoot()" class="folder-item py-1 text-success" style="cursor: pointer;">
                                                        📁 / (Carpeta raíz)
                                                    </div>
                                                </li>
                                                @foreach ($folders as $f)
                                                    @if ($f->id !== $folder->id && !$folder->hasDescendant($f->id))
                                                        <li>
                                                            <div onclick="navigateToFolder({{ $f->id }}, '{{ $f->name }}')" class="folder-item py-1" style="cursor: pointer;">
                                                                📁 {{ $f->name }}
                                                            </div>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenedor flex para alinear los botones -->
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="submit" class="btn btn-primary">Actualizar Carpeta</button>
                                    <!-- Botón Cancelar alineado a la derecha -->
                                    <a href="{{ route('folders.index') }}" class="btn btn-secondary">Cancelar</a>
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

    <script>
        let breadcrumbStack = [];
        const folderBeingEdited = {{ $folder->id }};

        function navigateToFolder(folderId, folderName) {
            breadcrumbStack.push({ id: folderId, name: folderName });
            updateBreadcrumb();
            loadSubfolders(folderId);
            document.getElementById('parent_id').value = folderId;
        }

        function selectAsRoot() {
            breadcrumbStack = [];
            updateBreadcrumb();
            document.getElementById('parent_id').value = '';
            loadRootFolders();
        }

        function updateBreadcrumb() {
            const breadcrumb = document.getElementById('breadcrumb');
            breadcrumb.innerHTML = '';

            const rootSpan = document.createElement('span');
            rootSpan.textContent = 'Inicio';
            rootSpan.style.cursor = 'pointer';
            rootSpan.onclick = () => selectAsRoot();
            breadcrumb.appendChild(rootSpan);

            breadcrumbStack.forEach((item, index) => {
                breadcrumb.appendChild(document.createTextNode(' / '));
                const span = document.createElement('span');
                span.textContent = item.name;
                span.onclick = () => goBackTo(index);
                breadcrumb.appendChild(span);
            });
        }

        function goBackTo(index) {
            const target = breadcrumbStack[index];
            breadcrumbStack = breadcrumbStack.slice(0, index + 1);
            updateBreadcrumb();
            loadSubfolders(target.id);
            document.getElementById('parent_id').value = target.id;
        }

        function loadSubfolders(parentId) {
            fetch(`/folders/subfolders?parent_id=${parentId}&current_folder_id=${folderBeingEdited}`)
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('folder-list');
                    list.innerHTML = '';

                    const rootItem = document.createElement('li');
                    rootItem.innerHTML = `<div onclick="selectAsRoot()" class="folder-item py-1 text-success" style="cursor: pointer;">📁 / (Carpeta raíz)</div>`;
                    list.appendChild(rootItem);

                    data.forEach(f => {
                        const li = document.createElement('li');
                        li.innerHTML = `<div onclick="navigateToFolder(${f.id}, '${f.name.replace(/'/g, "\\'")}')" class="folder-item py-1" style="cursor: pointer;">📁 ${f.name}</div>`;
                        list.appendChild(li);
                    });
                });
        }

        function loadRootFolders() {
            fetch(`/folders/subfolders?parent_id=&current_folder_id=${folderBeingEdited}`)
                .then(res => res.json())
                .then(data => {
                    const list = document.getElementById('folder-list');
                    list.innerHTML = '';

                    const rootItem = document.createElement('li');
                    rootItem.innerHTML = `<div onclick="selectAsRoot()" class="folder-item py-1 text-success" style="cursor: pointer;">📁 / (Carpeta raíz)</div>`;
                    list.appendChild(rootItem);

                    data.forEach(f => {
                        const li = document.createElement('li');
                        li.innerHTML = `<div onclick="navigateToFolder(${f.id}, '${f.name.replace(/'/g, "\\'")}')" class="folder-item py-1" style="cursor: pointer;">📁 ${f.name}</div>`;
                        list.appendChild(li);
                    });
                });
        }
    </script>

{{-- Estilos Personalizados --}}
<style>
    .form-group { margin-bottom: 15px; }
    .form-label { font-weight: bold; font-size: 14px; }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px;
        font-size: 14px;
    }
    .folder-item {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
        font-size: 13px;
        font-weight: bold;
        color: #000;
        display: flex;
        align-items: center;
        width: 100%;
        text-align: left;
        transition: all 0.3s ease-in-out;
        margin-bottom: 5px;
    }
    .folder-item:hover {
        background-color: #e1f5fe;
        border-left: 5px solid #0288d1;
        color: #0288d1;
    }
    .folder-item.active {
        background-color: #cceeff;
        border-left: 5px solid #0288d1;
        color: #000;
    }
    #breadcrumb span {
        font-weight: bold;
        color: #007bff;
        cursor: pointer;
        margin-right: 4px;
    }
    #breadcrumb span:hover {
        color: #0056b3;
        text-decoration: underline;
    }
</style>


</x-app-layout>
