<x-guest-layout>
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <x-guest.sidenav-guest />
            </div>
        </div>
    </div>
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 d-flex flex-column me-auto ms-4">
                            <div class="card card-plain mt-8">
                                <div class="card-header pb-0 text-left bg-transparent">
                                    <h3 class="font-weight-black text-dark display-6 text-center">Resetear Contraseña</h3>
                                </div>
                                <div class="card-body px-0">
                                    @if ($errors->any())
                                    <div>
                                        <div>Something went wrong!</div>

                                        <ul>
                                            @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endif
                                    @if (session('status'))
                                    <div class="mb-4 font-medium text-sm text-green-600">
                                        {{ session('status') }}
                                    </div>
                                    @endif
                                    <form role="form" action="/reset-password" method="POST" class="px-4">
                                        @csrf
                                        <input type="hidden" name="token" value="{{ $request->route('token') }}">
                                        <div class="mb-3">
                                            <input type="email" class="form-control" placeholder="Email"
                                                aria-label="Email" id="email" name="email"
                                                value="{{ old('email', $request->email) }}" required autofocus>
                                        </div>

                                        {{-- Campo: Contraseña --}}
                                        <div class="mb-3 position-relative">
                                            <input type="password" id="password" name="password" class="form-control pe-5"
                                                placeholder="Ingresar tu contraseña" aria-label="Password"
                                                oninput="validatePassword(this.value)" required>
                                            <span class="position-absolute end-0 top-50 translate-middle-y me-3" style="cursor: pointer;" onclick="togglePasswordVisibility('password', 'eye-password')">
                                                <i id="eye-password" class="fas fa-eye text-secondary"></i>
                                            </span>

                                            <div id="password-feedback"
                                                class="card p-2 shadow-sm position-absolute bg-white text-sm mt-1 border rounded d-none"
                                                style="z-index:1050; width: 100%;">
                                                <ul class="mb-0 ps-3">
                                                    <li id="length" class="text-danger">Mínimo 8 caracteres</li>
                                                    <li id="uppercase" class="text-danger">Una mayúscula</li>
                                                    <li id="lowercase" class="text-danger">Una minúscula</li>
                                                    <li id="number" class="text-danger">Un número</li>
                                                    <li id="symbol" class="text-danger">Un carácter especial (!@#$...)</li>
                                                </ul>
                                            </div>
                                        </div>
                                        {{-- Campo: Confirmar Contraseña --}}
                                        <div class="mb-3 position-relative">
                                            <input type="password" id="password_confirmation" name="password_confirmation"
                                                class="form-control pe-5" placeholder="Confirmar la contraseña" aria-label="Confirmar contraseña"
                                                oninput="checkMatch()" required>
                                            <span class="position-absolute end-0 top-50 translate-middle-y me-3" style="cursor: pointer;" onclick="togglePasswordVisibility('password_confirmation', 'eye-password-confirmation')">
                                                <i id="eye-password-confirmation" class="fas fa-eye text-secondary"></i>
                                            </span>
                                            <div id="match-feedback" class="text-danger small mt-1 d-none">Las contraseñas no coinciden</div>
                                        </div>

                                        {{-- ✅ Botón de envío --}}
                                        <div class="text-center">
                                            <button type="submit" class="my-4 mb-2 btn btn-dark btn-lg w-100">
                                                Enviar
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="position-absolute w-40 top-0 end-0 h-100 d-md-block d-none">
                            <div class="oblique-image position-absolute fixed-top ms-auto h-100 z-index-0 bg-cover ms-n8"
                                style="background: url('../assets/img/carrusel2.jpg') center center / cover no-repeat;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </main>
    <script>
        // 👁️ Mostrar / ocultar contraseña
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            const show = input.type === 'text';
            input.type = show ? 'password' : 'text';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        // ✅ Validación visual flotante de requisitos
        const rules = {
            length: pwd => pwd.length >= 8,
            uppercase: pwd => /[A-Z]/.test(pwd),
            lowercase: pwd => /[a-z]/.test(pwd),
            number: pwd => /[0-9]/.test(pwd),
            symbol: pwd => /[!@#$%^&*(),.?":{}|<>]/.test(pwd),
        };

        function validatePassword(pwd) {
            const feedback = document.getElementById('password-feedback');
            let invalid = false;

            Object.entries(rules).forEach(([id, test]) => {
                const el = document.getElementById(id);
                const passed = test(pwd);
                el.classList.toggle('text-success', passed);
                el.classList.toggle('text-danger', !passed);
                if (!passed) invalid = true;
            });

            feedback.classList.toggle('d-none', pwd === '' || !invalid);
            checkMatch(); // también actualiza coincidencia
        }

        // ✅ Coincidencia de confirmación
        function checkMatch() {
            const pwd = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;
            const feedback = document.getElementById('match-feedback');

            if (confirm !== '' && pwd !== confirm) {
                feedback.classList.remove('d-none');
            } else {
                feedback.classList.add('d-none');
            }
        }
    </script>


</x-guest-layout>