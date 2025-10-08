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
            <div class="page-header min-vh-100 split-right-vignette">
                <div class="container-fluid px-0">
                    <div class="row g-0 min-vh-10">
                        <div class="col-md-6 position-relative d-none d-md-block p-0 vh-100">
                            <div class="position-absolute top-0 start-0 w-100 h-100">
                                <div class="oblique-image position-absolute top-0 start-0 end-0 bottom-0 z-index-0"
                                    style="background: url('../assets/img/registrar.jpg') center center / cover no-repeat;">
                                    <!-- <div class="my-auto text-start max-width-350 ms-7">
                                        <h1 class="mt-3 text-white font-weight-bolder"> Carrera Ingeniería <br> Textil
                                        </h1>
                                    </div> -->
                                    <!--                                     <div class="text-start position-absolute fixed-bottom ms-7">
                                        <h6 class="text-white text-sm mb-5">Copyright © 2022 Corporate UI Design System
                                            by Creative Tim.</h6>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex flex-column mx-auto">
                            <div class="card card-plain mt-8 auth-box">
                                <div class="card-header pb-0 text-left bg-transparent">
                                    <h3 class="font-weight-black text-dark display-6">Registrarse</h3>
                                    <p class="mb-0">Mucho gusto! Por favor ingrese sus datos.</p>
                                </div>
                                <div class="card-body">
                                    <form role="form" method="POST" action="sign-up">
                                        @csrf
                                        <label>Nombres completos</label>
                                        <div class="mb-3">
                                            <input type="text" id="name" name="name" class="form-control"
                                                placeholder="Ingrese su nombre completo" value="{{old("name")}}"
                                                aria-label="Name" aria-describedby="name-addon">
                                            @error('name')
                                                <span class="text-danger text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <label>Correo</label>
                                        <div class="mb-3 position-relative">
                                            <input type="email" id="email-register" name="email"
                                                class="form-control pe-5" placeholder="Ingrese su correo"
                                                value="{{ old('email') }}" aria-label="Email"
                                                aria-describedby="email-addon">

                                            <div id="tooltip-email-register"
                                                class="tooltip-box card p-2 shadow-sm bg-white text-sm text-danger position-absolute">
                                                Ingresa un correo electrónico válido (ej: nombre@dominio.com)
                                            </div>
                                        </div>
                                        <!--                                         <label>Contraseña</label>
                                        <div class="mb-3">
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Crea una contraseña" aria-label="Password"
                                                aria-describedby="password-addon">
                                            @error('password')
                                            <span class="text-danger text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>-->
                                        <label>Contraseña</label>
                                        <div class="mb-3 position-relative">
                                            <input type="password" id="password" name="password"
                                                class="form-control pe-5" placeholder="Crea una contraseña"
                                                aria-label="Password">

                                            <span class="position-absolute end-0 top-50 translate-middle-y me-3"
                                                style="cursor: pointer;" onclick="togglePassword()">
                                                <i id="toggle-icon" class="fas fa-eye text-secondary"></i>
                                            </span>

                                            @error('password')
                                                <span class="text-danger text-sm">{{ $message }}</span>
                                            @enderror

                                            <div id="password-tooltip"
                                                class="tooltip-box card p-2 shadow-sm bg-white text-sm position-absolute">
                                                <ul class="mb-0 ps-3">
                                                    <li id="req-length" class="text-danger">Mínimo 8 caracteres</li>
                                                    <li id="req-lower" class="text-danger">Una letra minúscula</li>
                                                    <li id="req-upper" class="text-danger">Una letra mayúscula</li>
                                                    <li id="req-number" class="text-danger">Un número</li>
                                                    <li id="req-symbol" class="text-danger">Un carácter especial
                                                        (@$!%*?&)</li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="form-check form-check-info text-left mb-0">
                                            <input class="form-check-input" type="checkbox" name="terms" id="terms"
                                                required>
                                            <label class="font-weight-normal text-dark mb-0" for="terms">
                                                Estoy de acuerdo <a href="javascript:;"
                                                    class="text-dark font-weight-bold">Terminos y Condiciones</a>.
                                            </label>
                                            @error('terms')
                                                <span class="text-danger text-sm">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="text-center">
                                            <button type="submit"
                                                class="btn btn-dark w-100 mt-4 mb-3">Registrarse</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-4 text-xs mx-auto">
                                        ¿Ya tienes una cuenta?
                                        <a href="{{ route('sign-in') }}" class="text-dark font-weight-bold">Iniciar
                                            Sesión</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <style>
        .tooltip-box {
            display: none;
            top: calc(100% + 4px);
            left: 0;
            width: 100%;
            z-index: 1050;
            border: 1px solid #ccc;
            border-radius: 0.5rem;
        }

        .tooltip-box.show {
            display: block;
        }

        /* ===== Vignette MORADO en el LADO DERECHO sin pseudo-elementos ===== */
        :root {
            --right-purple: #C77BBD;
            /* morado del lado derecho */
            --base-bg: #FFFFFF;
            /* fondo base (izquierda) */

            /* controla el brillo/fade */
            --vigX: 85%;
            /* apertura horizontal (60–95%) */
            --vigY: 80%;
            /* apertura vertical   (60–95%) */
            --inner: 60%;
            /* tamaño de zona clara (menor = más grande) */
            --outer: 92%;
            /* hasta dónde se difumina (mayor = más suave) */
        }

        .page-header {
            position: relative;
        }

        /* aplica todo el efecto como background (no tapa el contenido) */
        .split-right-vignette {
            background:
                /* brillo en la mitad derecha */
                radial-gradient(ellipse var(--vigX) var(--vigY) at 75% 50%,
                    rgba(255, 255, 255, 1) var(--inner),
                    rgba(255, 255, 255, 0) var(--outer)) right / 50% 100% no-repeat,

                /* derecha morada, izquierda transparente */
                linear-gradient(90deg, transparent 0 50%, var(--right-purple) 50% 100%),

                /* base blanca debajo */
                var(--base-bg);
        }

        /* Si prefieres desactivar el efecto en móviles */
        @media (max-width: 767.98px) {
            .split-right-vignette {
                background: var(--right-purple);
            }
        }

        .split-right-vignette {
            height: 100vh;
        }

        /* mejor que min-height */
        .split-right-vignette .row {
            --bs-gutter-x: 0;
            --bs-gutter-y: 0;
        }

        .oblique-image {
            inset: 0;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
        }

        /* Controla el ancho del formulario sin tocar el layout ni el degradado */
        .split-right-vignette .auth-box {
            max-width: 410px;
            /* ajusta a gusto (p.ej. 380–520px) */
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        /* Opcional: centrado vertical suave en md+ (tu col ya es d-flex) */
        @media (min-width: 768px) {
            .split-right-vignette .col-md-4.d-flex.flex-column.mx-auto {
                align-items: center;
                /* centra la card horizontalmente dentro de la col */
            }

            .split-right-vignette .auth-box {
                margin-top: 0;
            }
        }

        /* (Opcional) en móvil hacerlo un pelín más angosto si quieres */
        @media (max-width: 767.98px) {
            .split-right-vignette .auth-box {
                max-width: 380px;
            }
        }
    </style>

    <script>
        const passwordInput = document.getElementById('password');
        const tooltip = document.getElementById('password-tooltip');

        passwordInput?.addEventListener('focus', () => tooltip.classList.add('show'));
        passwordInput?.addEventListener('blur', () => tooltip.classList.remove('show'));
        passwordInput?.addEventListener('input', updateTooltip);

        function setClass(id, valid) {
            const el = document.getElementById(id);
            el.classList.toggle('text-success', valid);
            el.classList.toggle('text-danger', !valid);
        }

        function updateTooltip() {
            const value = passwordInput.value;
            setClass('req-length', value.length >= 8);
            setClass('req-lower', /[a-z]/.test(value));
            setClass('req-upper', /[A-Z]/.test(value));
            setClass('req-number', /[0-9]/.test(value));
            setClass('req-symbol', /[@$!%*?&]/.test(value));
        }
    </script>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggle-icon');
            const isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
    <style>
        .tooltip-box {
            display: none;
            top: calc(100% + 4px);
            left: 0;
            width: 100%;
            z-index: 1050;
            border: 1px solid #dc3545;
            border-radius: 0.5rem;
        }

        .tooltip-box.show {
            display: block;
        }
    </style>

    <script>
        const emailInputRegister = document.getElementById('email-register');
        const tooltipRegister = document.getElementById('tooltip-email-register');

        function validateEmailRegister() {
            const value = emailInputRegister.value;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            tooltipRegister.classList.toggle('show', value !== '' && !regex.test(value));
        }

        emailInputRegister?.addEventListener('focus', validateEmailRegister);
        emailInputRegister?.addEventListener('blur', () => tooltipRegister.classList.remove('show'));
        emailInputRegister?.addEventListener('input', validateEmailRegister);
    </script>


</x-guest-layout>