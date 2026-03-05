<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     * 
     * Intercepta el error 419 (PAGE EXPIRED / Token CSRF expirado)
     * y redirige al login con un mensaje amigable.
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            if (!auth()->check()) {
                return redirect()->route('sign-in')
                    ->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.');
            }

            return redirect()->back()
                ->with('error', 'La página ha expirado. Por favor, intenta de nuevo.');
        }

        return parent::render($request, $exception);
    }

    /**
     * Manejo de peticiones no autenticadas.
     * 
     * Cuando una petición AJAX (fetch de un modal) falla por sesión expirada,
     * guardamos el REFERER (la página de administración donde estaba el usuario)
     * como URL "intended", NO la URL del fragmento de modal.
     * 
     * Así, después de re-autenticarse, el usuario regresa a la página
     * de administración (ej: /user/users-management) en lugar de la URL
     * del modal (ej: /user/5/edit) que se ve sin estilos.
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($this->shouldReturnJson($request, $exception)) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        $path = $request->path();

        // Detectar si la URL es un fragmento de modal:
        // - URLs que terminan en /edit (ej: /user/5/edit, /socios/3/edit)
        // - URLs que terminan en /create (ej: /user/create, /nichos/create)  
        // - URLs que terminan en /exhumar (ej: /asignaciones/5/exhumar)
        // - URLs con un ID seguido de nombre (ej: /socios/3, /fallecidos/5)
        // Estas son las rutas que devuelven fragmentos HTML para modales
        $isModalFragment = $request->ajax()
            || preg_match('#/(create|edit|exhumar)$#', $path)
            || preg_match('#/\d+$#', $path); // URLs como /socios/3, /nichos/5

        if ($isModalFragment) {
            // Guardar la URL de la página donde estaba el usuario (Referer),
            // no la URL del fragmento AJAX/modal
            $referer = $request->headers->get('referer');
            if ($referer) {
                session()->put('url.intended', $referer);
            }
            return redirect()->route('sign-in');
        }

        // Para peticiones normales: comportamiento por defecto de Laravel
        return redirect()->guest(route('sign-in'));
    }
}
