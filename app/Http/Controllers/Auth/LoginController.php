<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.signin');
    }

    public function store(Request $request)
    {
        // Validación de entrada
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Usuario no existe
        if (!$user) {
            return back()->withErrors([
                'email' => 'El usuario no existe.',
            ])->withInput($request->only('email'));
        }

        // Usuario está inactivo
        if (!$user->status) {
            return back()->withErrors([
                'email' => 'Su cuenta está desactivada. Contacte al administrador para reactivarla.',
            ])->withInput($request->only('email'));
        }

        // Cuenta bloqueada por intentos fallidos
        if ($user->failed_attempts >= 4) {
            $user->status = false; // ⛔ Se vuelve inactiva automáticamente
            $user->locked_at = now(); // (opcional) por si llevas control
            $user->save();
        
            return back()->withErrors([
                'password' => 'Demasiados intentos fallidos. Tu cuenta ha sido bloqueada y ahora está inactiva.',
            ])->withInput($request->only('email'))->with('account_locked', true);
        }        

        // Verificación de credenciales
        $credentials = $request->only('email', 'password');
        $remember = $request->rememberMe ? true : false;

        if (Auth::attempt($credentials, $remember)) {
            // Login exitoso: reiniciar intentos fallidos
            $user->update(['failed_attempts' => 0]);

            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        } else {
            // Falló: aumentar contador
            $user->increment('failed_attempts');

            return back()->withErrors([
                'password' => 'Credenciales incorrectas.',
            ])->withInput($request->only('email'));
        }
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/sign-in');
    }
}
