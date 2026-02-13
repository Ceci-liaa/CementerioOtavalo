<x-guest-layout>
    <div class="container position-sticky z-index-sticky top-0">
        <div class="row">
            <div class="col-12">
                <x-guest.sidenav-guest />
            </div>
        </div>
    </div>
    <main class="main-content mt-0">
        <section>
            <div class="page-header min-vh-100 split-left-purple">
                <div class="container">
                    <div class="row g-0">
                        <div class="col-12 col-md-6 col-xl-6 d-flex flex-column">
                            <div class="card card-plain mt-8 mx-auto auth-box">
                                <div class="card-header pb-0 text-left bg-transparent text-center">
                                    <h3 class="font-weight-black text-dark display-6">Bienvenido</h3>
                                    <p class="mb-0">Crea una nueva cuenta<br></p>
                                    <p class="mb-0">O inicia sesión con tus credenciales</p>
                                    <!--                                     <p class="mb-0">Email: <b>admin@corporateui.com</b></p>
                                    <p class="mb-0">Contraseña: <b>secret</b></p> -->
                                </div>

                                <div class="card-body">
                                    @if (session('success'))
                                        <div class="alert alert-success px-3 py-2 text-sm rounded mb-3"
                                            style="max-width: 400px;">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    <div class="text-center">
                                        @if ($errors->any())
                                            <div class="alert alert-danger px-3 py-2 text-sm rounded"
                                                style="max-width: 400px;">
                                                @foreach ($errors->all() as $error)
                                                    <div>{!! $error !!}</div> <!-- Mostrar errores con HTML -->
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <form role="form" class="text-start" method="POST" action="{{ route('sign-in') }}">
                                        @csrf
                                        <label>Correo</label>
                                        <div class="mb-3 position-relative">
                                            <input type="email" id="email-login" name="email" class="form-control pe-5"
                                                placeholder="Ingresar correo" value="{{ old('email') }}"
                                                aria-label="Email" aria-describedby="email-addon">

                                            <div id="tooltip-email-login"
                                                class="tooltip-box card p-2 shadow-sm bg-white text-sm text-danger position-absolute">
                                                Ingresa un correo electrónico válido (ej: nombre@dominio.com)
                                            </div>
                                        </div>

                                        <label>Contraseña</label>
                                        <div class="mb-3 position-relative">
                                            <input type="password" id="password" name="password" value=""
                                                class="form-control pe-5" placeholder="Ingresar Contraseña"
                                                aria-label="Password">

                                            <span class="position-absolute end-0 top-50 translate-middle-y me-3"
                                                style="cursor: pointer;" onclick="togglePasswordLogin()">
                                                <i id="toggle-icon-login" class="fas fa-eye text-secondary"></i>
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center">
                                            <!-- Mostrar el enlace de desbloquear cuenta solo si la cuenta está bloqueada -->
                                            @if (session('account_locked'))
                                                <a href="{{ route('password.request') }}"
                                                    class="text-xs font-weight-bold ms-1">Desbloquear cuenta</a>
                                            @endif

                                            <!-- Enlace para recuperar la contraseña -->
                                            <a href="{{ route('password.request') }}"
                                                class="text-xs font-weight-bold ms-auto">Olvidaste tu contraseña?</a>
                                        </div>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-dark w-100 mt-4 mb-3">Iniciar
                                                Sesión</button>
                                        </div>
                                    </form>
                                </div>
                                {{-- REGISTRO PÚBLICO DESHABILITADO --}}
                                {{-- <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-4 text-xs mx-auto">
                                        ¿No tienes una cuenta?
                                        <a href="{{ route('sign-up') }}"
                                            class="text-dark font-weight-bold">Registrarse</a>
                                    </p>
                                </div> --}}
                            </div>
                        </div>

                        <!-- Columna derecha (logo centrado y escalable) -->
                        <div class="col-12 col-md-6 col-xl-6 position-relative d-none d-md-block">
                            <div class="position-absolute top-0 end-0 h-100 w-100">
                                <div class="oblique-image bg-logo no-distort"></div>
                            </div>
                        </div>

                        <!-- <div class="col-md-6">
                            <div class="position-absolute w-40 top-0 end-0 h-100 d-md-block d-none">
                                <div class="oblique-image position-absolute fixed-top ms-auto h-100 z-index-0 bg-cover ms-n8"
                                    style="background: url('../assets/img/iniciarsesion.jpg') center center / cover no-repeat;">
                                    <div
                                        class="blur mt-12 p-4 text-center border border-white border-radius-md position-absolute fixed-bottom m-4">
                                        <h2 class="mt-3 text-dark font-weight-bold">Laboratorio de Calidad Textil</h2>
                                                                                <h6 class="text-dark text-sm mt-5">Copyright © 2022 Corporate UI Design System
                                            by Creative Tim.</h6>
                                    </div>
                                </div>
                            </div>
                        </div> -->
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
            border: 1px solid #dc3545;
            border-radius: 0.5rem;
        }

        .tooltip-box.show {
            display: block;
        }

        .oblique-image {
            position: absolute;
            inset: 0;
        }

        .bg-logo {
            background-image: url('../assets/img/iniciarsesion.jpg');
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Altura del logo dentro del contenedor (ajusta este %).
   Cuanto MENOS % pongas aquí, MÁS del ancho cabrá sin recorte. */
        .bg-logo.no-distort {
            --logo-height: 88%;
            /* prueba 90%, 88%, 85%... */
            background-size: auto var(--logo-height) !important;
        }

        
        /* ── Vignette morado en el lado izquierdo (centro blanco) ── */
        :root {
            --left-bg: #C77BBD;
            /* morado base (ajústalo) */
            --right-bg: #FFFFFF;
            /* lado derecho */
            --vignette-x: 85%;
            /* radio X del difuminado interno (60–95%) */
            --vignette-y: 80%;
            /* radio Y del difuminado interno (60–95%) */
            --inner-stop: 58%;
            /* tamaño del “cuadro” blanco (menor = más grande) */
            --outer-stop: 92%;
            /* hasta dónde se difumina (mayor = borde más suave) */
        }

        .split-left-purple {
            position: relative;
            background: var(--right-bg);
            /* base blanca */
        }

        /* 1) Pintamos la mitad izquierda de morado */
        .split-left-purple::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            background: linear-gradient(90deg, var(--left-bg) 0 50%, transparent 50%);
        }

        /* 2) Encima, un “vignette” que aclara el centro del lado izquierdo */
        .split-left-purple::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            /* sobre el morado pero bajo el contenido */
            pointer-events: none;

            /* Un radial en la MITAD IZQUIERDA que va de blanco (centro) a transparente (bordes),
     dejando ver el morado sólo en los bordes, como en tu imagen. */
            background:
                radial-gradient(var(--vignette-x) var(--vignette-y) at 25% 50%,
                    rgba(255, 255, 255, 1) var(--inner-stop),
                    rgba(255, 255, 255, 0) var(--outer-stop)) left / 50% 100% no-repeat;
        }

        /* El contenido va por encima */
        .split-left-purple>.container {
            position: relative;
            z-index: 2;
        }

        /* En móvil: todo morado (o déjalo blanco si quieres) */
        @media (max-width: 767.98px) {
            .split-left-purple {
                background: var(--left-bg);
            }

            .split-left-purple::before,
            .split-left-purple::after {
                background: none;
            }
        }

        /* Controla el ancho del formulario sin tocar layout */
        .auth-box {
            max-width: 410px;
            /* ajusta 460–560px a gusto */
            width: 100%;
        }

        /* Opcional: centrado vertical suave en pantallas medianas+ sin mover nada más */
        @media (min-width: 768px) {
            .auth-box {
                margin-top: 0;
            }

            /* si lo quieres más centrado vertical: descomenta la siguiente línea */
            /* .auth-box{ margin-top: auto; margin-bottom: auto; } */
        }
    </style>


    <script>
        function togglePasswordLogin() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggle-icon-login');
            const isVisible = input.type === 'text';
            input.type = isVisible ? 'password' : 'text';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>

    <script>
        const emailInputLogin = document.getElementById('email-login');
        const tooltipLogin = document.getElementById('tooltip-email-login');

        function validateEmailLogin() {
            const value = emailInputLogin.value;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            tooltipLogin.classList.toggle('show', value !== '' && !regex.test(value));
        }

        emailInputLogin?.addEventListener('focus', validateEmailLogin);
        emailInputLogin?.addEventListener('blur', () => tooltipLogin.classList.remove('show'));
        emailInputLogin?.addEventListener('input', validateEmailLogin);
    </script>

</x-guest-layout>