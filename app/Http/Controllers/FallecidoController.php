<?php

namespace App\Http\Controllers;

use App\Models\Fallecido;
use Illuminate\Http\Request;
use App\Models\Comunidad;
use App\Models\Genero;
use App\Models\EstadoCivil;

class FallecidoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = Fallecido::with(['comunidad.parroquia.canton','genero','estadoCivil'])
            ->orderBy('apellidos')->orderBy('nombres');

        if ($q !== '') {
            $query->where(function($w) use ($q) {
                $w->where('cedula','ILIKE',"%{$q}%")
                  ->orWhere('nombres','ILIKE',"%{$q}%")
                  ->orWhere('apellidos','ILIKE',"%{$q}%");
            });
        }

        $fallecidos = $query->paginate(10)->withQueryString();

        return view('fallecidos.fallecido-index', compact('fallecidos'));
    }

    public function create()
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id','nombre']);
        $generos     = Genero::orderBy('nombre')->get(['id','nombre']);
        $estados     = EstadoCivil::orderBy('nombre')->get(['id','nombre']);

        return view('fallecidos.fallecido-create', compact('comunidades','generos','estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'comunidad_id'     => 'nullable|exists:comunidades,id',
            'genero_id'        => 'nullable|exists:generos,id',
            'estado_civil_id'  => 'nullable|exists:estados_civiles,id',
            'cedula'           => 'required|string|max:20|unique:fallecidos,cedula',
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'fecha_fallecimiento' => 'nullable|date',
            'observaciones'    => 'nullable|string',
        ]);

        try {
            Fallecido::create([
                'comunidad_id'     => $request->comunidad_id,
                'genero_id'        => $request->genero_id,
                'estado_civil_id'  => $request->estado_civil_id,
                'cedula'           => $request->cedula,
                'nombres'          => $request->nombres,
                'apellidos'        => $request->apellidos,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'fecha_fallecimiento' => $request->fecha_fallecimiento,
                'observaciones'    => $request->observaciones,
                'created_by'       => auth()->id(),
            ]);

            return redirect()->route('fallecidos.index')->with('success','Fallecido registrado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error','Error al registrar: '.$e->getMessage());
        }
    }

    public function edit(Fallecido $fallecido)
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id','nombre']);
        $generos     = Genero::orderBy('nombre')->get(['id','nombre']);
        $estados     = EstadoCivil::orderBy('nombre')->get(['id','nombre']);

        return view('fallecidos.fallecido-edit', compact('fallecido','comunidades','generos','estados'));
    }

    public function update(Request $request, Fallecido $fallecido)
    {
        $request->validate([
            'comunidad_id'     => 'nullable|exists:comunidades,id',
            'genero_id'        => 'nullable|exists:generos,id',
            'estado_civil_id'  => 'nullable|exists:estados_civiles,id',
            'cedula'           => 'required|string|max:20|unique:fallecidos,cedula,' . $fallecido->id,
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'fecha_nacimiento' => 'nullable|date',
            'fecha_fallecimiento' => 'nullable|date',
            'observaciones'    => 'nullable|string',
        ]);

        try {
            $fallecido->update($request->all());
            return redirect()->route('fallecidos.index')->with('success','Fallecido actualizado.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error','Error al actualizar: '.$e->getMessage());
        }
    }

    public function show(Fallecido $fallecido)
    {
        $fallecido->load(['comunidad.parroquia.canton','genero','estadoCivil','creador']);
        return view('fallecidos.fallecido-show', compact('fallecido'));
    }

    public function destroy(Fallecido $fallecido)
    {
        try {
            $fallecido->delete();
            return redirect()->route('fallecidos.index')->with('success','Fallecido eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('fallecidos.index')->with('error','Error al eliminar: '.$e->getMessage());
        }
    }
}