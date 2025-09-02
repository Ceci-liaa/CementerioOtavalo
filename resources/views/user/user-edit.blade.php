<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <x-app.navbar />

        <div class="px-5 py-4 container-fluid">
            <div class="mt-4 row">
                <div class="col-12">
                    <div class="alert alert-dark text-sm" role="alert">
                        <strong style="font-size: 24px;">Editar Usuario</strong>
                    </div>
                    <div class="card">
                        <!-- 🔹 Mostrar mensajes de éxito, error o advertencia -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{ session('info') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('users.update', $user) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Nombre</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Teléfono</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                                </div>

                                <div class="col-md-6">
                                    <label>Ubicación</label>
                                    <input type="text" name="location" value="{{ old('location', $user->location) }}" class="form-control">
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label>Rol</label>
                                        <select name="role_id" class="form-control">
                                            @foreach ($roles as $roleId => $roleName)
                                                <option value="{{ $roleId }}"
                                                    @if ($user->getRoleNames()->first() == $roleName) selected @endif>
                                                    {{ ucfirst($roleName) }}
                                                </option>
                                            @endforeach
                                        </select>
                                </div>

                                <div class="col-md-6">
                                    <label>Estado</label>
                                    <select name="status" class="form-control">
                                        <option value="1" @if($user->status) selected @endif>Activo</option>
                                        <option value="0" @if(!$user->status) selected @endif>Inactivo</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary mt-4">Guardar Cambios</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <x-app.footer />
    </main>
</x-app-layout>
