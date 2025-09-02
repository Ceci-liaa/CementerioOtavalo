<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{
    // 🔹 Mostrar la vista del perfil del usuario autenticado
    public function index()
    {
        $user = Auth::user();
        return view('laravel-examples.user-profile', compact('user'));
    }

    // 🔹 Procesar la actualización del perfil del usuario autenticado
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validar datos
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'about' => 'nullable|string',
        ]);

        try {
            // Actualizar datos
            $user->update($request->only(['name', 'email', 'location', 'phone', 'about']));

            return redirect()->route('users.profile')->with('success', 'Perfil actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el perfil.');
        }
    }
}
