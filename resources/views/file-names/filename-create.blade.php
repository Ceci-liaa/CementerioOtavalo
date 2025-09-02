<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Crear Nuevo Nombre de Archivo</strong>
                    </div>

                    <!-- ✅ Mensajes de éxito y error -->
                    @if (session('success'))
                        <div id="success-message" class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div id="error-message" class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('file_names.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre del Archivo</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>

                                <!-- Contenedor flex para alinear los botones -->
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                    <!-- Botón Cancelar alineado a la derecha -->
                                    <a href="{{ route('file_names.index') }}" class="btn btn-secondary">Cancelar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <x-app.footer />
    </main>

    <!-- ✅ Script para ocultar los mensajes después de 5 segundos -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(function () {
                let successMessage = document.getElementById('success-message');
                if (successMessage) {
                    successMessage.style.transition = "opacity 0.5s";
                    successMessage.style.opacity = 0;
                    setTimeout(() => successMessage.remove(), 500);
                }

                let errorMessage = document.getElementById('error-message');
                if (errorMessage) {
                    errorMessage.style.transition = "opacity 0.5s";
                    errorMessage.style.opacity = 0;
                    setTimeout(() => errorMessage.remove(), 500);
                }
            }, 5000);
        });
    </script>
</x-app-layout>
