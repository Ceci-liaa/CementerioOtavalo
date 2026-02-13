<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Exports\GenericExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = strtolower($request->get('search'));
            $query->where(function($q) use ($searchTerm) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchTerm}%"])
                  ->orWhereRaw('LOWER(codigo_usuario) LIKE ?', ["%{$searchTerm}%"]);
            });
        }

        // =================================================================
        // CORRECCIÓN AQUÍ: Cambiamos ->get() por ->paginate(10)
        // Esto soluciona el error "Collection::total does not exist"
        // =================================================================
        $users = $query->orderBy('codigo_usuario', 'asc')->paginate(10); 

        // Obtener Roles para el Modal
        $roles = Role::pluck('name', 'id'); 

        return view('user.users-management', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::pluck('name', 'id');
        return view('user.user-create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
            ],
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'status' => 'nullable|boolean',
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'Debe ingresar un correo válido',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una letra mayúscula, una minúscula, un número y un carácter especial.',
            'role_id.required' => 'Debe seleccionar un rol',
            'role_id.exists' => 'El rol seleccionado no es válido',
        ]);

        try {
            $role = Role::find($request->role_id);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => \Hash::make($request->password),
                'phone' => $request->phone,
                'location' => $request->location,
                'role_id' => $role->id,
                'status' => $request->has('status') ? true : false,
            ]);

            // Asignar rol usando Spatie Permission
            $user->syncRoles([$role->name]);

            $mensaje = "El usuario {$user->name} ({$user->codigo_usuario}) fue creado correctamente.";

            return redirect()->route('users-management')->with('success', $mensaje);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
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
            'role_id' => 'required|exists:roles,id',
        ]);

        try {
            $role = Role::find($request->role_id);

            if ($role) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'location' => $request->location,
                    'phone' => $request->phone,
                    'about' => $request->about,
                    'status' => $request->status,
                    'role_id' => $role->id, 
                ]);

                $user->syncRoles([$role->name]);
            }

            $mensaje = "El usuario {$user->name} ({$user->codigo_usuario}) fue actualizado correctamente.";

            return redirect()->route('users-management')->with('success', $mensaje);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar la información: ' . $e->getMessage());
        }
    }

    public function toggleStatus(User $user)
    {
        try {
            $user->status = !$user->status;
            $user->save();
            return redirect()->route('users-management')->with('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al actualizar el estado.');
        }
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name', 'id');
        return view('user.user-edit', compact('user', 'roles'));
    }

    public function generateReports(Request $request)
    {
        if (!isset($request->users) || empty($request->users)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un usuario.');
        }

        // Para reportes SÍ usamos get() porque queremos todos los seleccionados, no paginados
        $users = User::whereIn('id', $request->users)
                     ->orderBy('codigo_usuario', 'asc')
                     ->get();

        if ($request->report_type == 'pdf') {
            $pdf = Pdf::loadView('user.users-report', compact('users'));
            return $pdf->download('usuarios_reporte_'.date('YmdHis').'.pdf');
        } elseif ($request->report_type == 'excel') {
            $headings = ['Código de Usuario', 'Nombre', 'Email', 'Teléfono', 'Ubicación', 'Rol', 'Estado'];
            return Excel::download(new GenericExport($users, $headings), 'usuarios_reporte_'.date('YmdHis').'.xlsx');
        }

        return redirect()->back()->with('error', 'Tipo de reporte no válido.');
    }

}
