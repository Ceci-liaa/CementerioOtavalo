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
                        <div class="col-xl-4 col-md-6 d-flex flex-column mx-auto">
                            <div class="card card-plain mt-8">
                                <div class="card-header pb-0 text-left bg-transparent">
                                    <h3 class="font-weight-black text-dark display-6 text-center">Olvidaste tu
                                        Contraseña?</h3>
                                    <p class="mb-0 text-center">Ingresa tu correo electrónico a continuación</p>
                                </div>
                                @if ($errors->any())
                                    <div class="alert alert-danger text-sm" role="alert">
                                        @foreach ($errors->all() as $error)
                                            {{ $error }}
                                        @endforeach
                                    </div>
                                @endif
                                @if (session('status'))
                                    <div class="alert alert-info text-sm" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div class="alert alert-danger text-sm" role="alert">
                                        {{ session('error') }}
                                    </div>
                                @endif
                                <div class="card-body">
                                    <form role="form" action="/forgot-password" method="POST">
                                        {{ csrf_field() }}
                                        <div class="mb-3 position-relative">
                                            <input type="email" class="form-control pe-5" placeholder="Email"
                                                aria-label="Email" id="email-recovery" name="email"
                                                value="{{ old('email') }}" required autofocus>

                                            <div id="tooltip-email-recovery"
                                                class="tooltip-box card p-2 shadow-sm bg-white text-sm text-danger position-absolute">
                                                Ingresa un correo válido (ej: nombre@dominio.com)
                                            </div>
                                        </div>

                                        <div class="text-center">
                                            <button type="submit" class="my-4 mb-2 btn btn-dark btn-lg w-100">Enlace de
                                                recuperación</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="position-absolute w-40 top-0 end-0 h-100 d-md-block d-none">
                                <div class="oblique-image position-absolute fixed-top ms-auto h-100 z-index-0 bg-cover ms-n8"
                                    style="background: url('../assets/img/carrusel1.jpg') center center / cover no-repeat;">
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
            border: 1px solid #dc3545;
            border-radius: 0.5rem;
            background-color: #fff;
            color: #dc3545;
            font-size: 0.875rem;
        }

        .tooltip-box.show {
            display: block;
        }

        .form-control.invalid {
            border: 1px solid #dc3545;
        }

        .form-control.valid {
            border: 1px solid #28a745;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('email-recovery');
            const tooltip = document.getElementById('tooltip-email-recovery');
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            input?.addEventListener('input', function () {
                const isValid = regex.test(input.value);
                if (!isValid) {
                    tooltip.classList.add('show');
                    input.classList.add('invalid');
                    input.classList.remove('valid');
                } else {
                    tooltip.classList.remove('show');
                    input.classList.remove('invalid');
                    input.classList.add('valid');
                }
            });
        });
    </script>

</x-guest-layout>