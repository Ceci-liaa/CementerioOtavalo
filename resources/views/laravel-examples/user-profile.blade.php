<x-app-layout>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

        <div class="top-0 bg-cover z-index-n1 min-height-100 max-height-200 h-25 position-absolute w-100 start-0 end-0"
            style="background-image: url('../../../assets/img/header-blue-purple.jpg'); background-position: bottom;">
        </div>
        <x-app.navbar />
        
        <div class="px-5 py-4 container-fluid">
            <!-- 🔹 FORMULARIO CORREGIDO -->
            <form action="{{ route('users.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Campo oculto para diferenciar actualización de perfil -->
                <input type="hidden" name="profile_update" value="true">

                <div class="mt-5 mb-5 mt-lg-9 row justify-content-center">
                    <div class="col-lg-9 col-12">
                        <div class="card card-body" id="profile">
                            <div class="top-0 rounded-2 position-absolute start-0 w-100 h-100"></div>
                            <div class="row z-index-2 justify-content-center align-items-center">
                                <div class="col-sm-auto col-4">
                                    <div class="avatar avatar-xl position-relative text-center">
                                        <!-- 🔹 Ícono en lugar de imagen -->
                                        <i class="fas fa-user-circle" style="font-size: 80px; color: #6c757d;"></i>
                                    </div>
                                </div>
                                <div class="col-sm-auto col-8 my-auto">
                                    <div class="h-100">
                                        <h5 class="mb-1 font-weight-bolder">
                                            {{ auth()->user()->name }}
                                        </h5>
                                        <p class="mb-0 font-weight-bold text-sm">
                                            {{ auth()->user()->getRoleNames()->first() ?? 'Sin Rol' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🔹 MENSAJES DE SESIÓN -->
                <div class="row justify-content-center">
                    <div class="col-lg-9 col-12">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mb-5 row justify-content-center">
                    <div class="col-lg-9 col-12">
                        <div class="card" id="basic-info">
                            <div class="card-header">
                                <h5>Información Básica</h5>
                            </div>
                            <div class="pt-0 card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <label for="name">Nombres Completos</label>
                                        <input type="text" name="name" id="name"
                                            value="{{ old('name', auth()->user()->name) }}" class="form-control">
                                    </div>
                                    <div class="col-6">
                                        <label for="email">Correo</label>
                                        <input type="email" name="email" id="email"
                                            value="{{ old('email', auth()->user()->email) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label for="location">Locación</label>
                                        <input type="text" name="location" id="location"
                                            value="{{ old('location', auth()->user()->location) }}" class="form-control">
                                    </div>
                                    <div class="col-6">
                                        <label for="phone">Teléfono</label>
                                        <input type="text" name="phone" id="phone"
                                            value="{{ old('phone', auth()->user()->phone) }}" class="form-control">
                                    </div>
                                </div>
                                <div class="row p-2">
                                    <label for="about">Acerca de mí</label>
                                    <textarea name="about" id="about" rows="5" class="form-control">{{ old('about', auth()->user()->about) }}</textarea>
                                </div>
                                <button type="submit" class="mt-6 mb-0 btn btn-primary float-end">Guardar cambios</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <x-app.footer />

        <!-- 🔹 Script para ocultar mensajes después de 3 segundos -->
        <script>
            setTimeout(function() {
                document.querySelectorAll('.alert-dismissible').forEach(alert => {
                    alert.style.transition = "opacity 0.5s";
                    alert.style.opacity = 0;
                    setTimeout(() => alert.remove(), 500);
                });
            }, 3000);
        </script>

    </main>
    
</x-app-layout>
