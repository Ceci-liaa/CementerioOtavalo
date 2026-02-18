<div class="modal-header bg-dark text-white">
    <h5 class="modal-title fw-bold text-white">Nuevo Usuario</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<form method="POST" action="{{ route('users.store') }}" class="text-start">
    @csrf
    
    <div class="modal-body">
        
        {{-- ALERTA INFORMATIVA --}}
        <div class="alert alert-info py-2 mb-3 text-xs">
            <i class="fas fa-info-circle me-1"></i> El Código de usuario se genera automáticamente.
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 text-xs">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3">
            {{-- FILA 1: NOMBRE COMPLETO (ancho completo) --}}
            <div class="col-12">
                <label>Nombres completos</label>
                <input type="text" id="name" name="name" class="form-control"
                    placeholder="Ingrese su nombre completo" value="{{old("name")}}"
                    aria-label="Name" aria-describedby="name-addon">
                @error('name')
                    <span class="text-danger text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- FILA 2: EMAIL + CONTRASEÑA (2 columnas) --}}
            <div class="col-md-6">
                <label>Correo</label>
                <div class="position-relative">
                    <input type="email" id="email-register" name="email"
                        class="form-control pe-5" placeholder="Ingrese su correo"
                        value="{{ old('email') }}" aria-label="Email"
                        aria-describedby="email-addon">

                    <div id="tooltip-email-register"
                        class="tooltip-box card p-2 shadow-sm bg-white text-sm text-danger position-absolute">
                        Ingresa un correo electrónico válido (ej: nombre@dominio.com)
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label>Contraseña</label>
                <div class="position-relative">
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
                            <li id="req-symbol" class="text-danger">Un carácter especial (@$!%*?&)</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- FILA 3: TELÉFONO + UBICACIÓN (2 columnas) --}}
            <div class="col-md-6">
                <label>Teléfono</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" 
                       placeholder="0987654321" maxlength="10"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
            </div>

            <div class="col-md-6">
                <label>Ubicación</label>
                <input type="text" name="location" value="{{ old('location') }}" class="form-control" 
                       placeholder="Ciudad, País">
            </div>

            {{-- FILA 4: ROL + ESTADO (2 columnas) --}}
            <div class="col-md-8">
                <label>Rol <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    <option value="">Seleccionar rol...</option>
                    @foreach($roles as $id => $name)
                        <option value="{{ $id }}" @selected(old('role_id')==$id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Estado</label>
                <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" 
                           {{ old('status', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="statusSwitch">
                        Activo
                    </label>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-success fw-bold">Guardar Usuario</button>
    </div>
</form>

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
