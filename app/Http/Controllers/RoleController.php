<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // Listado de roles (con paginación)
    public function index()
    {
        $roles = Role::with('permissions')->latest('id')->paginate(12);
        return view('roles.role-index', compact('roles')); // roles/index.blade.php
    }

    public function create()
    {
        return view('roles.role-create'); // roles/create.blade.php
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:100','unique:roles,name'],
        ]);

        Role::create([
            'name'       => $validated['name'],
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('roles.index')
            ->with('ok', 'Rol creado correctamente.');
    }

    public function edit(Role $role)
    {
        return view('roles.role-edit', compact('role')); // roles/edit.blade.php
    }

    // Actualizar rol (solo nombre)
    public function update(Request $request, Role $role)
    {
        // ⛔ No renombrar Administrador
        if ($role->name === 'Administrador' && $request->name !== 'Administrador') {
            return back()->with('error', 'No se puede renombrar el rol Administrador.');
        }

        $validated = $request->validate([
            'name' => [
                'required','string','max:100',
                Rule::unique('roles','name')->ignore($role->id),
            ],
        ]);

        $role->update([
            'name'       => $validated['name'],
            'guard_name' => $role->guard_name ?? 'web',
        ]);

        return redirect()
            ->route('roles.index')
            ->with('ok', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Administrador') {
            return back()->with('error', 'No se puede eliminar el rol Administrador.');
        }

        // (Opcional) evitar borrar si tiene usuarios asignados
        // if ($role->users()->exists()) {
        //     return back()->with('error', 'No se puede eliminar: el rol está asignado a usuarios.');
        // }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('ok', 'Rol eliminado correctamente.');
    }
}
