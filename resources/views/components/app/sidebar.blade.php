<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 bg-slate-900 fixed-start" id="sidenav-main"
    style="z-index: 1050;">

    {{-- CABECERA (RESTAURADA A TU FUENTE ORIGINAL) --}}
    <div class="sidenav-header text-center pb-2">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>

        <a class="navbar-brand m-0" href="{{ route('dashboard') }}">
            <div class="d-flex flex-column align-items-center justify-content-center h-100">
                {{-- TU CÓDIGO EXACTO --}}
                <span class="font-weight-bolder text-white text-lg tracking-wide"
                    style="letter-spacing: 1px;">UNORICO</span>
                <span class="font-weight-normal text-white text-sm opacity-8">SAMASHUNCHIK</span>
            </div>
        </a>
    </div>

    <hr class="horizontal light mt-0 mb-2">

    <div class="collapse navbar-collapse px-3 w-auto" id="sidenav-collapse-main" style="height: calc(100vh - 100px);">
        <ul class="navbar-nav">

            {{-- DASHBOARD --}}
            <li class="nav-item">
                <a class="nav-link {{ is_current_route('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                        <i class="ni ni-tv-2 text-primary text-sm opacity-10"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            {{-- ============================================================== --}}
            {{-- 1. ADMINISTRACIÓN --}}
            {{-- ============================================================== --}}
            @if(auth()->user()->can('ver usuario') || auth()->user()->can('ver rol') || auth()->user()->can('gestionar permisos'))
                <li class="nav-item mt-2">
                    <a data-bs-toggle="collapse" href="#adminMenu" class="nav-link text-white" aria-controls="adminMenu"
                        role="button" aria-expanded="{{ Request::is('user*', 'roles*') ? 'true' : 'false' }}">
                        <div
                            class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                            <i class="ni ni-settings-gear-65 text-light text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1 font-weight-bold">Administración</span>
                        <i class="fas fa-chevron-down ms-auto text-xs"></i>
                    </a>

                    <div class="collapse {{ Request::is('user*', 'roles*') ? 'show' : '' }}" id="adminMenu">
                        <ul class="nav ms-4 ps-2 d-flex flex-column gap-1">
                            @can('ver usuario')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ is_current_route('users-management') ? 'active-sub' : '' }}"
                                        href="{{ route('users-management') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-users text-info text-xs"></i> </span>
                                        <span class="sidenav-normal"> Usuarios </span>
                                    </a>
                                </li>
                            @endcan

                            @can('ver rol')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ is_current_route('roles.index') ? 'active-sub' : '' }}"
                                        href="{{ route('roles.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-user-tag text-warning text-xs"></i>
                                        </span>
                                        <span class="sidenav-normal"> Roles </span>
                                    </a>
                                </li>
                            @endcan

                            @can('gestionar permisos')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ is_current_route('roles.permissions.manager') ? 'active-sub' : '' }}"
                                        href="{{ route('roles.permissions.manager') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-key text-success text-xs"></i> </span>
                                        <span class="sidenav-normal"> Permisos </span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endif

            {{-- ============================================================== --}}
            {{-- 2. UBICACIÓN --}}
            {{-- ============================================================== --}}
            @if(auth()->user()->can('ver canton') || auth()->user()->can('ver parroquia') || auth()->user()->can('ver comunidad'))
                <li class="nav-item mt-2">
                    <a data-bs-toggle="collapse" href="#locationMenu" class="nav-link text-white"
                        aria-controls="locationMenu" role="button"
                        aria-expanded="{{ Request::is('cantones*', 'parroquias*', 'comunidades*') ? 'true' : 'false' }}">
                        <div
                            class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                            <i class="ni ni-map-big text-warning text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1 font-weight-bold">Ubicación</span>
                        <i class="fas fa-chevron-down ms-auto text-xs"></i>
                    </a>

                    <div class="collapse {{ Request::is('cantones*', 'parroquias*', 'comunidades*') ? 'show' : '' }}"
                        id="locationMenu">
                        <ul class="nav ms-4 ps-2 d-flex flex-column gap-1">
                            @can('ver canton')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('cantones*') ? 'active-sub' : '' }}"
                                        href="{{ route('cantones.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-city text-primary text-xs"></i> </span>
                                        <span class="sidenav-normal"> Cantones </span>
                                    </a>
                                </li>
                            @endcan

                            @can('ver parroquia')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('parroquias*') ? 'active-sub' : '' }}"
                                        href="{{ route('parroquias.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-map-marked-alt text-info text-xs"></i>
                                        </span>
                                        <span class="sidenav-normal"> Parroquias </span>
                                    </a>
                                </li>
                            @endcan

                            @can('ver comunidad')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('comunidades*') ? 'active-sub' : '' }}"
                                        href="{{ route('comunidades.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-people-roof text-success text-xs"></i>
                                        </span>
                                        <span class="sidenav-normal"> Comunidades </span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endif

            {{-- ============================================================== --}}
            {{-- 3. CEMENTERIO --}}
            {{-- ============================================================== --}}
            @if(auth()->user()->can('ver socio') || auth()->user()->can('ver fallecido') || auth()->user()->can('ver nicho') || auth()->user()->can('ver asignacion') || auth()->user()->can('ver bloque'))
                <li class="nav-item mt-2">
                    <a data-bs-toggle="collapse" href="#cementerioMenu" class="nav-link text-white"
                        aria-controls="cementerioMenu" role="button"
                        aria-expanded="{{ Request::is('socios*', 'fallecidos*', 'nichos*', 'asignaciones*', 'bloques*') ? 'true' : 'false' }}">
                        <div
                            class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                            <i class="ni ni-building text-success text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1 font-weight-bold">Cementerio</span>
                        <i class="fas fa-chevron-down ms-auto text-xs"></i>
                    </a>

                    <div class="collapse {{ Request::is('socios*', 'fallecidos*', 'nichos*', 'asignaciones*', 'bloques*') ? 'show' : '' }}"
                        id="cementerioMenu">
                        <ul class="nav ms-4 ps-2 d-flex flex-column gap-1">
                            @can('ver socio')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('socios*') ? 'active-sub' : '' }}"
                                        href="{{ route('socios.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-id-card text-warning text-xs"></i> </span>
                                        <span class="sidenav-normal"> Socios </span>
                                    </a>
                                </li>
                            @endcan

                            @can('ver fallecido')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('fallecidos*') ? 'active-sub' : '' }}"
                                        href="{{ route('fallecidos.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-cross text-white text-xs"></i> </span>
                                        <span class="sidenav-normal"> Fallecidos </span>
                                    </a>
                                </li>
                            @endcan

                            @can('ver asignacion')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('asignaciones*') ? 'active-sub' : '' }}"
                                        href="{{ route('asignaciones.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-file-signature text-info text-xs"></i>
                                        </span>
                                        <span class="sidenav-normal"> Asignaciones </span>
                                    </a>
                                </li>
                            @endcan

                            @can('ver nicho')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('nichos*') ? 'active-sub' : '' }}"
                                        href="{{ route('nichos.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-monument text-primary text-xs"></i>
                                        </span>
                                        <span class="sidenav-normal"> Nichos </span>
                                    </a>
                                </li>
                            @endcan
                            @can('ver bloque')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('bloques*') ? 'active-sub' : '' }}"
                                        href="{{ route('bloques.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-cubes text-success text-xs"></i> </span>
                                        <span class="sidenav-normal"> Bloques </span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endif

            {{-- ============================================================== --}}
            {{-- 4. FINANCIERO --}}
            {{-- ============================================================== --}}
            @if(auth()->user()->can('ver pago') || auth()->user()->can('ver factura') || auth()->user()->can('ver servicio') || auth()->user()->can('ver beneficio'))
                <li class="nav-item mt-2">
                    <a data-bs-toggle="collapse" href="#finanzasMenu" class="nav-link text-white"
                        aria-controls="finanzasMenu" role="button"
                        aria-expanded="{{ Request::is('pagos*', 'facturas*', 'servicios*', 'beneficios*') ? 'true' : 'false' }}">
                        <div
                            class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                            <i class="ni ni-money-coins text-info text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1 font-weight-bold">Financiero</span>
                        <i class="fas fa-chevron-down ms-auto text-xs"></i>
                    </a>

                    <div class="collapse {{ Request::is('pagos*', 'facturas*', 'servicios*', 'beneficios*') ? 'show' : '' }}"
                        id="finanzasMenu">
                        <ul class="nav ms-4 ps-2 d-flex flex-column gap-1">
                            @can('ver pago')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('pagos.general') ? 'active-sub' : '' }}"
                                        href="{{ route('pagos.general') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-receipt text-success text-xs"></i> </span>
                                        <span class="sidenav-normal"> Historial Pagos </span>
                                    </a>
                                </li>
                            @endcan

                            @can('ver factura')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('facturas*') ? 'active-sub' : '' }}"
                                        href="{{ route('facturas.index') }}">
                                        <span class="sidemenu-icon"> <i
                                                class="fas fa-file-invoice-dollar text-warning text-xs"></i> </span>
                                        <span class="sidenav-normal"> Facturación </span>
                                    </a>
                                </li>
                            @endcan

                            @can('ver servicio')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('servicios*') ? 'active-sub' : '' }}"
                                        href="{{ route('servicios.index') }}">
                                        <span class="sidemenu-icon"> <i class="fas fa-tools text-light text-xs"></i> </span>
                                        <span class="sidenav-normal"> Servicios </span>
                                    </a>
                                </li>
                            @endcan

                            @can('ver beneficio')
                                <li class="nav-item">
                                    <a class="nav-link sub-link {{ request()->routeIs('beneficios*') ? 'active-sub' : '' }}"
                                        href="{{ route('beneficios.index') }}">
                                        <span class="sidemenu-icon"> <i
                                                class="fas fa-hand-holding-heart text-danger text-xs"></i> </span>
                                        <span class="sidenav-normal"> Beneficios </span>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                </li>
            @endif

            {{-- ============================================================== --}}
            {{-- 5. AUDITORÍA (INDEPENDIENTE) --}}
            {{-- ============================================================== --}}
            @can('ver auditoria')
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('auditoria.index') ? 'active' : '' }}"
                        href="{{ route('auditoria.index') }}">
                        <div
                            class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                            <i class="ni ni-notification-70 text-danger text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1 font-weight-bold text-white">Auditoría del Sistema</span>
                    </a>
                </li>
            @endcan

            {{-- ============================================================== --}}
            {{-- 6. PANEL USUARIO (MI PERFIL) --}}
            {{-- ============================================================== --}}
            @can('ver perfil')
                <li class="nav-item mt-2">
                    <a data-bs-toggle="collapse" href="#profileMenu" class="nav-link text-white" aria-controls="profileMenu"
                        role="button"
                        aria-expanded="{{ Request::is('laravel-examples/user-profile*') ? 'true' : 'false' }}">
                        <div
                            class="icon icon-shape icon-sm px-0 text-center d-flex align-items-center justify-content-center">
                            <i class="ni ni-single-02 text-dark text-sm opacity-10"></i>
                        </div>
                        <span class="nav-link-text ms-1 font-weight-bold">Panel Usuario</span>
                        <i class="fas fa-chevron-down ms-auto text-xs"></i>
                    </a>

                    <div class="collapse {{ Request::is('laravel-examples/user-profile*') ? 'show' : '' }}"
                        id="profileMenu">
                        <ul class="nav ms-4 ps-2 d-flex flex-column gap-1">
                            <li class="nav-item">
                                <a class="nav-link sub-link {{ is_current_route('users.profile') ? 'active-sub' : '' }}"
                                    href="{{ route('users.profile') }}">
                                    <span class="sidemenu-icon"> <i class="fas fa-user-circle text-info text-xs"></i>
                                    </span>
                                    <span class="sidenav-normal"> Mi Perfil </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endcan

        </ul>
    </div>
</aside>

<style>
    /* Estilos base del Sidebar */
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    /* Scrollbar */
    #sidenav-main .navbar-collapse::-webkit-scrollbar {
        width: 5px;
    }

    #sidenav-main .navbar-collapse::-webkit-scrollbar-track {
        background: transparent;
    }

    #sidenav-main .navbar-collapse::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
    }

    /* ESTILOS DE SUB-ENLACES */
    .sidenav .collapse .nav-link.sub-link {
        color: #e2e8f0 !important;
        /* Blanco Grisaceo */
        font-weight: 400;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        opacity: 0.9;
        display: flex;
        align-items: center;
    }

    .sidenav .collapse .nav-link.sub-link:hover {
        color: #ffffff !important;
        opacity: 1;
        transform: translateX(5px);
        background: rgba(255, 255, 255, 0.1);
        border-radius: 5px;
    }

    /* Enlace activo */
    .sidenav .collapse .nav-link.sub-link.active-sub {
        color: #ffffff !important;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 0.5rem;
    }

    /* ESTILO PARA EL ICONO DEL SUBMENÚ */
    .sidemenu-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        margin-right: 8px;
    }

    /* Flecha del acordeón */
    .nav-link[aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }

    .nav-link .fa-chevron-down {
        transition: transform 0.3s ease;
    }
</style>