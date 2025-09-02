<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;


class UserController extends Controller
{
    public function index()
    {
        $users = User::all(); // Cargamos el rol del usuario correctamente
        return view('user.users-management', compact('users'));
    }


    public function update(Request $request, User $user)
    {
        $request->validate([

            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'location' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'about' => 'nullable|string',
            'status' => 'required|boolean',
            'role_id' => 'required|exists:roles,id', // Validamos que el rol existe
        ]);

        try {
            $role = Role::find($request->role_id);

            if ($role) {
                // Actualizar usuario con el nuevo `role_id`
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'location' => $request->location,
                    'phone' => $request->phone,
                    'about' => $request->about,
                    'status' => $request->status,
                    'role_id' => $role->id, 
                ]);

                // Sincronizar el rol en Spatie (model_has_roles)
                $user->syncRoles([$role->name]);
            }

            return redirect()->route('users-management')->with('success', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar la información: ' . $e->getMessage());
        }
    }


    public function toggleStatus(User $user)
    {
        try {
            $user->status = !$user->status; // Cambia de 1 a 0 o viceversa
            $user->save();
            return redirect()->route('users-management')->with('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el estado.');
        }
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'id'); // Obtener roles con ID y Nombre
        return view('user.user-edit', compact('user', 'roles'));
    }

    // Antiguo
    // {
    //     $roles = Role::all(); // Obtener todos los roles disponibles
    //     return view('user.user-edit', compact('user', 'roles')); // Retorna la vista de edición
    // }
}
