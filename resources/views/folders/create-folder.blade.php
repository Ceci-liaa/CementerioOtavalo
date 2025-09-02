<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Crear Nueva Carpeta</strong>
                    </div>

                    <!-- 🔹 Mensajes de error y éxito -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show fade-message" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show fade-message" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('folders.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Nombre de la Carpeta</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label>Ubicación de la Carpeta</label>

                                        <div class="mb-2">
                                            <strong>📍 Ruta actual:</strong>
                                            <nav id="breadcrumb" class="d-inline"></nav>
                                        </div>

                                        <input type="hidden" name="parent_id" id="parent_id" value="">

                                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                            <div id="folder-selector" class="d-flex flex-column gap-2">
                                                {{-- Aquí se cargan dinámicamente las carpetas --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenedor flex para alinear los botones -->
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="submit" class="btn btn-primary">Crear Carpeta</button>
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
                document.querySelectorAll('.fade-message').forEach(alert => {
                    alert.style.transition = "opacity 0.5s";
                    alert.style.opacity = 0;
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000); // Desaparece después de 5 segundos
        });
    </script>

    <script>
        let currentPath = []; // Ruta de carpetas
        let selectedFolderId = null;

        document.addEventListener("DOMContentLoaded", () => {
            loadFolders(); // Al inicio, carga carpetas raíz
        });

        function goToLevel(index) {
            // Si index es -1 → volver a raíz
            if (index === -1) {
                currentPath = [];
                selectedFolderId = null;
                document.getElementById("parent_id").value = "";
                updateBreadcrumb();
                loadFolders();
            } else {
                const newPath = currentPath.slice(0, index + 1);
                const target = newPath[newPath.length - 1];
                currentPath = newPath;
                selectedFolderId = target.id;
                document.getElementById("parent_id").value = target.id;
                updateBreadcrumb();
                loadFolders(target.id);
            }
        }

        function updateBreadcrumb() {
            const breadcrumbContainer = document.getElementById("breadcrumb");
            breadcrumbContainer.innerHTML = "";

            const inicio = document.createElement("a");
            inicio.href = "#";
            inicio.textContent = "Inicio";
            inicio.onclick = (e) => {
                e.preventDefault();
                goToLevel(-1);
            };
            breadcrumbContainer.appendChild(inicio);

            currentPath.forEach((folder, index) => {
                const separator = document.createTextNode(" / ");
                breadcrumbContainer.appendChild(separator);

                const link = document.createElement("a");
                link.href = "#";
                link.textContent = folder.name;
                link.onclick = (e) => {
                    e.preventDefault();
                    goToLevel(index);
                };
                breadcrumbContainer.appendChild(link);
            });
        }

        function selectFolder(folderId, folderName) {
            selectedFolderId = folderId;
            document.getElementById("parent_id").value = folderId;
            highlightSelection(folderId);
        }

        function highlightSelection(folderId) {
            // Quitar estado activo a todos
            document.querySelectorAll(".folder-btn").forEach(btn => {
                btn.classList.remove("active");
            });

            // Agregar estado activo al seleccionado
            const selected = document.querySelector(`#folder-${folderId}`);
            if (selected) {
                selected.classList.add("active");
            }
        }

        function loadFolders(parentId = null) {
            const container = document.getElementById("folder-selector");
            container.innerHTML = `<div class="text-muted">Cargando...</div>`;

            fetch(`/folders/${parentId ?? 0}/children`)
                .then(res => res.json())
                .then(data => {
                    container.innerHTML = "";
                    if (data.length === 0) {
                        container.innerHTML = `<div class="text-muted">No hay subcarpetas.</div>`;
                        return;
                    }

                    data.forEach(folder => {
                        const btn = document.createElement("button");
                        btn.className = "btn btn-outline-secondary text-start folder-btn";
                        btn.style.textAlign = "left";
                        btn.id = `folder-${folder.id}`;
                        btn.innerHTML = `📁 ${folder.name}`;
                        btn.onclick = () => {
                            // Ir hacia la siguiente vista (entrar en carpeta)
                            currentPath.push({ id: folder.id, name: folder.name });
                            selectFolder(folder.id, folder.name);
                            updateBreadcrumb();
                            loadFolders(folder.id);
                        };
                        container.appendChild(btn);
                    });

                    // Si hay una selección previa, resáltala
                    if (selectedFolderId) highlightSelection(selectedFolderId);
                });
        }
    </script>

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

        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
        }

        /* Botones de selección de carpetas (tipo input) */
        .folder-btn {
            background-color: #ffffff;
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
        }

        .folder-btn:hover {
            background-color: #e1f5fe;
            /* Celeste clarito */
            border-left: 5px solid #0288d1;
            color: #0288d1;
        }

        .folder-btn.active {
            background-color: #cceeff;
            border-left: 5px solid #0288d1;
            color: #000;
        }

        /* Breadcrumb enlaces */
        #breadcrumb a {
            font-weight: bold;
            color: #007bff;
            text-decoration: none;
            padding: 2px 4px;
            transition: color 0.2s ease-in-out;
        }

        #breadcrumb a:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>

</x-app-layout>