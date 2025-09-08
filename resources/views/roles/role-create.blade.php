<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="row">
                <div class="col-12">

                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Crear Rol</strong>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('roles.store') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label class="form-label">Nombre del rol</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                                    @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-success">💾 Guardar</button>
                                    <a href="{{ route('roles.index') }}" class="btn btn-secondary">⬅️ Volver</a>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <x-app.footer />
    </main>
</x-app-layout>
