<?php

use Illuminate\Support\Facades\Route;

// Controladores de Autenticación y Perfil
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

// Controladores de Seguridad
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\PermissionManagerController;
use App\Http\Controllers\AuditController;

// Controladores del Negocio (Cementerio)
use App\Http\Controllers\CantonController;
use App\Http\Controllers\ParroquiaController;
use App\Http\Controllers\ComunidadController;
use App\Http\Controllers\SocioController;
use App\Http\Controllers\FallecidoController;
use App\Http\Controllers\BloqueController;
use App\Http\Controllers\BloqueGeomController;
use App\Http\Controllers\NichoController;
use App\Http\Controllers\SocioNichoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\BeneficioController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\PagoController;

/*
|--------------------------------------------------------------------------
| Web Routes - SISTEMA DE GESTIÓN CEMENTERIO
|--------------------------------------------------------------------------
*/

// ========================================================================
// 1. RUTAS PÚBLICAS Y DE AUTENTICACIÓN (Guest)
// ========================================================================

Route::get('/', function () { return redirect('/dashboard'); })->middleware('auth');

Route::middleware('guest')->group(function () {
    Route::get('/signin', function () { return view('account-pages.signin'); })->name('signin');
    Route::get('/signup', function () { return view('account-pages.signup'); })->name('signup');
    Route::get('/sign-up', [RegisterController::class, 'create'])->name('sign-up');
    Route::post('/sign-up', [RegisterController::class, 'store']);
    Route::get('/sign-in', [LoginController::class, 'create'])->name('sign-in');
    Route::post('/sign-in', [LoginController::class, 'store']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');


// ========================================================================
// 2. RUTAS GENERALES (Dashboard y Perfil Propio)
// ========================================================================

Route::middleware(['auth'])->group(function () {
    // Dashboard (Todos los autenticados pueden ver)
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    
    // Vistas estáticas (Si las usas)
    Route::get('/tables', function () { return view('tables'); })->name('tables');
    Route::get('/wallet', function () { return view('wallet'); })->name('wallet');
    Route::get('/RTL', function () { return view('RTL'); })->name('RTL');
    Route::get('/profile-static', function () { return view('account-pages.profile'); })->name('profile');

    // PERFIL DE USUARIO (Editar sus propios datos)
    // Protegido con los permisos 'ver perfil' y 'editar perfil'
    Route::get('/laravel-examples/user-profile', [ProfileController::class, 'index'])
        ->name('users.profile')->middleware('can:ver perfil');
    Route::put('/laravel-examples/user-profile/update', [ProfileController::class, 'update'])
        ->name('users.profile.update')->middleware('can:editar perfil');
});


// ========================================================================
// 3. SEGURIDAD AVANZADA (Solo Administradores)
// ========================================================================

Route::middleware(['auth', 'role:Administrador'])->group(function () {
    
    // Gestor de Permisos (Matriz de Checkboxes)
    Route::get('/roles/permissions-manager', [PermissionManagerController::class, 'index'])
        ->name('roles.permissions.manager')->middleware('can:gestionar permisos');
    Route::post('/roles/permissions-manager', [PermissionManagerController::class, 'update'])
        ->name('roles.permissions.manager.update')->middleware('can:gestionar permisos');

    // CRUD de Roles
    Route::resource('roles', RoleController::class)->except(['show']); // Index, create, store, edit, update, destroy
    
    // Asignación manual (Legacy)
    Route::get('/roles/{role}/permissions', [RolePermissionController::class, 'edit'])->name('roles.permissions.edit');
    Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'update'])->name('roles.permissions.update');
});


// ========================================================================
// 4. GESTIÓN DE USUARIOS DEL SISTEMA
// ========================================================================

Route::middleware(['auth'])->group(function () {
    // Ver Lista
    Route::get('/user/users-management', [UserController::class, 'index'])
        ->name('users-management')->middleware('can:ver usuario');

    // Editar Usuario
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])
        ->name('users.edit')->middleware('can:editar usuario');
    Route::put('/user/{user}/update', [UserController::class, 'update'])
        ->name('users.update')->middleware('can:editar usuario');

    // [CRÍTICO] Cambiar Estado (Banear)
    Route::put('/user/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status')->middleware('can:cambiar estado usuario');

    // Reportes
    Route::post('/users/reports', [UserController::class, 'generateReports'])
        ->name('users.generateReports')->middleware('can:reportar usuario');
});


// ========================================================================
// 5. UBICACIÓN (Cantones, Parroquias, Comunidades)
// ========================================================================

Route::middleware(['auth'])->group(function () {
    
    // --- CANTONES ---
    Route::resource('cantones', CantonController::class)->except(['show']); // Usar middleware en __construct o aquí
    // Aplicando permisos específicos a rutas resource manualmente para claridad:
    Route::get('cantones', [CantonController::class, 'index'])->name('cantones.index')->middleware('can:ver canton');
    Route::get('cantones/create', [CantonController::class, 'create'])->name('cantones.create')->middleware('can:crear canton');
    Route::post('cantones', [CantonController::class, 'store'])->name('cantones.store')->middleware('can:crear canton');
    Route::get('cantones/{canton}/edit', [CantonController::class, 'edit'])->name('cantones.edit')->middleware('can:editar canton');
    Route::put('cantones/{canton}', [CantonController::class, 'update'])->name('cantones.update')->middleware('can:editar canton');
    Route::delete('cantones/{canton}', [CantonController::class, 'destroy'])->name('cantones.destroy')->middleware('can:eliminar canton');
    Route::get('cantones/{canton}', [CantonController::class, 'show'])->name('cantones.show')->middleware('can:ver canton');

    // --- PARROQUIAS ---
    Route::get('parroquias', [ParroquiaController::class, 'index'])->name('parroquias.index')->middleware('can:ver parroquia');
    Route::get('parroquias/create', [ParroquiaController::class, 'create'])->name('parroquias.create')->middleware('can:crear parroquia');
    Route::post('parroquias', [ParroquiaController::class, 'store'])->name('parroquias.store')->middleware('can:crear parroquia');
    Route::get('parroquias/{parroquia}/edit', [ParroquiaController::class, 'edit'])->name('parroquias.edit')->middleware('can:editar parroquia');
    Route::put('parroquias/{parroquia}', [ParroquiaController::class, 'update'])->name('parroquias.update')->middleware('can:editar parroquia');
    Route::delete('parroquias/{parroquia}', [ParroquiaController::class, 'destroy'])->name('parroquias.destroy')->middleware('can:eliminar parroquia');
    Route::get('parroquias/{parroquia}', [ParroquiaController::class, 'show'])->name('parroquias.show')->middleware('can:ver parroquia');
    
    Route::post('/parroquias/reportes', [ParroquiaController::class, 'generateReports'])->name('parroquias.reports')->middleware('can:reportar parroquia');
    Route::get('cantones/{canton}/parroquias', [ParroquiaController::class, 'byCanton'])->name('cantones.parroquias');

    // --- COMUNIDADES ---
    Route::get('comunidades', [ComunidadController::class, 'index'])->name('comunidades.index')->middleware('can:ver comunidad');
    Route::get('comunidades/create', [ComunidadController::class, 'create'])->name('comunidades.create')->middleware('can:crear comunidad');
    Route::post('comunidades', [ComunidadController::class, 'store'])->name('comunidades.store')->middleware('can:crear comunidad');
    Route::get('comunidades/{comunidad}/edit', [ComunidadController::class, 'edit'])->name('comunidades.edit')->middleware('can:editar comunidad');
    Route::put('comunidades/{comunidad}', [ComunidadController::class, 'update'])->name('comunidades.update')->middleware('can:editar comunidad');
    Route::delete('comunidades/{comunidad}', [ComunidadController::class, 'destroy'])->name('comunidades.destroy')->middleware('can:eliminar comunidad');
    Route::get('comunidades/{comunidad}', [ComunidadController::class, 'show'])->name('comunidades.show')->middleware('can:ver comunidad');
    
    Route::post('comunidades/reports', [ComunidadController::class, 'reports'])->name('comunidades.reports')->middleware('can:reportar comunidad');
});


// ========================================================================
// 6. GESTIÓN DE PERSONAS (Socios y Fallecidos)
// ========================================================================

Route::middleware(['auth'])->group(function () {

    // --- SOCIOS ---
    Route::get('/socios', [SocioController::class, 'index'])->name('socios.index')->middleware('can:ver socio');
    Route::get('/socios/create', [SocioController::class, 'create'])->name('socios.create')->middleware('can:crear socio');
    Route::post('/socios', [SocioController::class, 'store'])->name('socios.store')->middleware('can:crear socio');
    Route::get('/socios/{socio}', [SocioController::class, 'show'])->name('socios.show')->middleware('can:ver socio');
    Route::get('/socios/{socio}/edit', [SocioController::class, 'edit'])->name('socios.edit')->middleware('can:editar socio');
    Route::match(['put', 'patch'], '/socios/{socio}', [SocioController::class, 'update'])->name('socios.update')->middleware('can:editar socio');
    Route::delete('/socios/{socio}', [SocioController::class, 'destroy'])->name('socios.destroy')->middleware('can:eliminar socio');
    
    Route::post('socios/reports', [SocioController::class, 'reports'])->name('socios.reports')->middleware('can:reportar socio');

    // --- FALLECIDOS ---
    Route::get('/fallecidos', [FallecidoController::class, 'index'])->name('fallecidos.index')->middleware('can:ver fallecido');
    Route::get('/fallecidos/create', [FallecidoController::class, 'create'])->name('fallecidos.create')->middleware('can:crear fallecido');
    Route::post('/fallecidos', [FallecidoController::class, 'store'])->name('fallecidos.store')->middleware('can:crear fallecido');
    Route::get('/fallecidos/{fallecido}', [FallecidoController::class, 'show'])->name('fallecidos.show')->middleware('can:ver fallecido');
    Route::get('/fallecidos/{fallecido}/edit', [FallecidoController::class, 'edit'])->name('fallecidos.edit')->middleware('can:editar fallecido');
    Route::match(['put', 'patch'], '/fallecidos/{fallecido}', [FallecidoController::class, 'update'])->name('fallecidos.update')->middleware('can:editar fallecido');
    Route::delete('/fallecidos/{fallecido}', [FallecidoController::class, 'destroy'])->name('fallecidos.destroy')->middleware('can:eliminar fallecido');
    
    Route::post('fallecidos/reports', [FallecidoController::class, 'reports'])->name('fallecidos.reports')->middleware('can:reportar fallecido');
});


// ========================================================================
// 7. INFRAESTRUCTURA Y SERVICIOS
// ========================================================================

Route::middleware(['auth'])->group(function () {

    // --- BLOQUES ---
    Route::get('/bloques', [BloqueController::class, 'index'])->name('bloques.index')->middleware('can:ver bloque');
    Route::get('/bloques/create', [BloqueController::class, 'create'])->name('bloques.create')->middleware('can:crear bloque');
    Route::post('/bloques', [BloqueController::class, 'store'])->name('bloques.store')->middleware('can:crear bloque');
    Route::get('/bloques/{bloque}', [BloqueController::class, 'show'])->name('bloques.show')->middleware('can:ver bloque');
    Route::get('/bloques/{bloque}/edit', [BloqueController::class, 'edit'])->name('bloques.edit')->middleware('can:editar bloque');
    Route::match(['put', 'patch'], '/bloques/{bloque}', [BloqueController::class, 'update'])->name('bloques.update')->middleware('can:editar bloque');
    Route::delete('/bloques/{bloque}', [BloqueController::class, 'destroy'])->name('bloques.destroy')->middleware('can:eliminar bloque');
    Route::post('bloques/reports', [BloqueController::class, 'reports'])->name('bloques.reports')->middleware('can:reportar bloque');

    // GeoJSON (Técnico)
    Route::get('/bloques_geom/{id}/geojson', [BloqueGeomController::class, 'geojson'])->name('bloques_geom.geojson')->whereNumber('id');

    // --- NICHOS ---
    Route::get('/nichos', [NichoController::class, 'index'])->name('nichos.index')->middleware('can:ver nicho');
    Route::get('/nichos/create', [NichoController::class, 'create'])->name('nichos.create')->middleware('can:crear nicho');
    Route::post('/nichos', [NichoController::class, 'store'])->name('nichos.store')->middleware('can:crear nicho');
    Route::get('/nichos/{nicho}', [NichoController::class, 'show'])->name('nichos.show')->middleware('can:ver nicho');
    Route::get('/nichos/{nicho}/edit', [NichoController::class, 'edit'])->name('nichos.edit')->middleware('can:editar nicho');
    Route::put('/nichos/{nicho}', [NichoController::class, 'update'])->name('nichos.update')->middleware('can:editar nicho');
    Route::delete('/nichos/{nicho}', [NichoController::class, 'destroy'])->name('nichos.destroy')->middleware('can:eliminar nicho');
    Route::post('nichos/reports', [NichoController::class, 'reports'])->name('nichos.reports')->middleware('can:reportar nicho');
    
    // QR de Nichos
    Route::get('nichos/{nicho}/qr', [NichoController::class, 'downloadQr'])->name('nichos.qr')->middleware('can:ver qr nicho');
    Route::get('nichos/{nicho}/qr-image', [NichoController::class, 'downloadQrImage'])->name('nichos.qr.image')->middleware('can:ver qr nicho');

    // --- BENEFICIOS ---
    Route::resource('beneficios', BeneficioController::class); // Se pueden aplicar permisos en constructor también
    Route::post('beneficios/reports', [BeneficioController::class, 'reports'])->name('beneficios.reports')->middleware('can:reportar beneficio');

    // --- SERVICIOS ---
    Route::resource('servicios', ServicioController::class);
    Route::post('servicios/reports', [ServicioController::class, 'reports'])->name('servicios.reports')->middleware('can:reportar servicio');
    
    // --- SOCIO-NICHO (Relación auxiliar) ---
    Route::resource('socio-nicho', SocioNichoController::class);
});


// ========================================================================
// 8. ASIGNACIONES Y EXHUMACIONES (Gestión Operativa)
// ========================================================================

Route::middleware(['auth'])->group(function () {
    
    // CRUD Asignaciones
    Route::get('/asignaciones', [AsignacionController::class, 'index'])
        ->name('asignaciones.index')->middleware('can:ver asignacion');
    Route::get('/asignaciones/create', [AsignacionController::class, 'create'])
        ->name('asignaciones.create')->middleware('can:crear asignacion');
    Route::post('/asignaciones', [AsignacionController::class, 'store'])
        ->name('asignaciones.store')->middleware('can:crear asignacion');
    Route::get('/asignaciones/{id}', [AsignacionController::class, 'show'])
        ->name('asignaciones.show')->middleware('can:ver asignacion');
    Route::get('/asignaciones/{id}/edit', [AsignacionController::class, 'edit'])
        ->name('asignaciones.edit')->middleware('can:editar asignacion');
    Route::put('/asignaciones/{id}', [AsignacionController::class, 'update'])
        ->name('asignaciones.update')->middleware('can:editar asignacion');
    
    // Eliminar Asignación (Liberar nicho por error)
    Route::delete('/asignaciones/{nicho_id}/{fallecido_id}', [AsignacionController::class, 'destroy'])
        ->name('asignacion.destroy')->middleware('can:eliminar asignacion');

    // [CRÍTICO] EXHUMACIÓN
    Route::get('/asignaciones/{id}/exhumar', [AsignacionController::class, 'exhumarForm'])
        ->name('asignaciones.exhumarForm')->middleware('can:exhumar cuerpo');
    Route::post('/asignaciones/exhumar', [AsignacionController::class, 'exhumar'])
        ->name('asignaciones.exhumar')->middleware('can:exhumar cuerpo');

    // Reportes y Certificados
    Route::get('/asignaciones/reporte-general', [AsignacionController::class, 'pdfReporteGeneral'])
        ->name('asignaciones.pdf.general')->middleware('can:reportar asignacion');
    Route::get('/asignaciones/reporte-exhumados', [AsignacionController::class, 'pdfReporteExhumados'])
        ->name('asignaciones.pdf.exhumados')->middleware('can:reportar asignacion');
    
    // Certificado Individual
    Route::get('/asignaciones/certificado/{nicho_id}/{fallecido_id}', [AsignacionController::class, 'pdfCertificadoExhumacion'])
        ->name('asignaciones.pdf.certificado')->middleware('can:generar certificado');
});


// ========================================================================
// 9. GESTIÓN FINANCIERA (Pagos y Facturas)
// ========================================================================

Route::middleware(['auth'])->group(function () {

    // --- PAGOS (RECIBOS) ---
    // Historial general y Ver
    Route::get('pagos-general', [PagoController::class, 'general'])->name('pagos.general')->middleware('can:ver pago');
    Route::get('recibos/{recibo}', [PagoController::class, 'show'])->name('pagos.show')->middleware('can:ver pago');
    
    // Historial de un Socio Específico
    Route::get('/pagos/historial/{socio}', [PagoController::class, 'historialSocio'])
        ->name('pagos.historial_socio')->middleware('can:ver historial socio');

    // Crear Pago (Cobrar)
    Route::get('pagos/nuevo-pago', [PagoController::class, 'create'])->name('pagos.create')->middleware('can:crear pago');
    Route::get('socios/{socio}/pagos', [PagoController::class, 'index'])->name('pagos.index')->middleware('can:crear pago');
    Route::post('socios/{socio}/pagos', [PagoController::class, 'store'])->name('pagos.store')->middleware('can:crear pago');

    // Editar / Eliminar Pago
    Route::get('recibos/{recibo}/edit', [PagoController::class, 'edit'])->name('pagos.edit')->middleware('can:editar pago');
    Route::put('recibos/{recibo}', [PagoController::class, 'update'])->name('pagos.update')->middleware('can:editar pago');
    Route::delete('recibos/{recibo}', [PagoController::class, 'destroy'])->name('pagos.destroy')->middleware('can:eliminar pago'); // Puede ser "anular" también


    // --- FACTURAS ---
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index')->middleware('can:ver factura');
    Route::get('/facturas/{factura}', [FacturaController::class, 'show'])->name('facturas.show')->middleware('can:ver factura');
    
    // Crear borrador
    Route::get('/facturas/create', [FacturaController::class, 'create'])->name('facturas.create')->middleware('can:crear factura');
    Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store')->middleware('can:crear factura');
    
    // Editar
    Route::get('/facturas/{factura}/edit', [FacturaController::class, 'edit'])->name('facturas.edit')->middleware('can:editar factura');
    Route::put('/facturas/{factura}', [FacturaController::class, 'update'])->name('facturas.update')->middleware('can:editar factura');
    
    // [CRÍTICO] Acciones Financieras
    Route::put('/facturas/{factura}/emitir', [FacturaController::class, 'emitir'])
        ->name('facturas.emitir')->middleware('can:emitir factura');
        
    Route::put('/facturas/{factura}/anular', [FacturaController::class, 'anular'])
        ->name('facturas.anular')->middleware('can:anular factura');
        
    Route::get('/facturas/{factura}/pdf', [FacturaController::class, 'generarPdf'])
        ->name('facturas.pdf')->middleware('can:descargar factura');
});


// ========================================================================
// 10. AUDITORÍA
// ========================================================================

Route::middleware(['auth', 'permission:ver auditoria'])->group(function () {
    Route::get('/audits', [AuditController::class, 'index'])->name('auditoria.index');
});