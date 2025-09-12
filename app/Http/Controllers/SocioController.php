<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Socio;
use App\Models\Comunidad;
use App\Models\Genero;
use App\Models\EstadoCivil;

class SocioController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = Socio::with(['comunidad.parroquia.canton', 'genero', 'estadoCivil'])
            ->orderBy('apellidos')->orderBy('nombres');

        // Postgres: ILIKE | MySQL: LIKE
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('cedula', 'ILIKE', "%{$q}%")
                  ->orWhere('nombres', 'ILIKE', "%{$q}%")
                  ->orWhere('apellidos', 'ILIKE', "%{$q}%");
            });
        }

        // 👇 paginator (->links() en la vista)
        $socios = $query->paginate(10)->withQueryString();

        return view('socios.socio-index', compact('socios'));
    }

    public function create()
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id','nombre']);
        $generos     = Genero::orderBy('nombre')->get(['id','nombre']);
        $estados     = EstadoCivil::orderBy('nombre')->get(['id','nombre']);

        return view('socios.socio-create', compact('comunidades','generos','estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'comunidad_id'     => 'nullable|exists:comunidades,id',
            'genero_id'        => 'nullable|exists:generos,id',
            'estado_civil_id'  => 'nullable|exists:estados_civiles,id',
            'cedula'           => 'required|string|max:20|unique:socios,cedula',
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'telefono'         => 'nullable|string|max:30',
            'direccion'        => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'fecha_nac'        => 'nullable|date',
            'es_representante' => 'nullable|boolean',
        ]);

        try {
            Socio::create([
                'comunidad_id'     => $request->comunidad_id,
                'genero_id'        => $request->genero_id,
                'estado_civil_id'  => $request->estado_civil_id,
                'cedula'           => $request->cedula,
                'nombres'          => $request->nombres,
                'apellidos'        => $request->apellidos,
                'telefono'         => $request->telefono,
                'direccion'        => $request->direccion,
                'email'            => $request->email,
                'fecha_nac'        => $request->fecha_nac,
                'es_representante' => (bool)$request->es_representante,
                'created_by'       => auth()->id(),
            ]);

            return redirect()->route('socios.index')->with('success', 'Socio creado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

    public function edit(Socio $socio)
    {
        $comunidades = Comunidad::orderBy('nombre')->get(['id','nombre']);
        $generos     = Genero::orderBy('nombre')->get(['id','nombre']);
        $estados     = EstadoCivil::orderBy('nombre')->get(['id','nombre']);

        return view('socios.socio-edit', compact('socio','comunidades','generos','estados'));
    }

    public function update(Request $request, Socio $socio)
    {
        $request->validate([
            'comunidad_id'     => 'nullable|exists:comunidades,id',
            'genero_id'        => 'nullable|exists:generos,id',
            'estado_civil_id'  => 'nullable|exists:estados_civiles,id',
            'cedula'           => 'required|string|max:20|unique:socios,cedula,' . $socio->id,
            'nombres'          => 'required|string|max:255',
            'apellidos'        => 'required|string|max:255',
            'telefono'         => 'nullable|string|max:30',
            'direccion'        => 'nullable|string|max:255',
            'email'            => 'nullable|email|max:255',
            'fecha_nac'        => 'nullable|date',
            'es_representante' => 'nullable|boolean',
        ]);

        try {
            $socio->update([
                'comunidad_id'     => $request->comunidad_id,
                'genero_id'        => $request->genero_id,
                'estado_civil_id'  => $request->estado_civil_id,
                'cedula'           => $request->cedula,
                'nombres'          => $request->nombres,
                'apellidos'        => $request->apellidos,
                'telefono'         => $request->telefono,
                'direccion'        => $request->direccion,
                'email'            => $request->email,
                'fecha_nac'        => $request->fecha_nac,
                'es_representante' => (bool)$request->es_representante,
            ]);

            return redirect()->route('socios.index')->with('success', 'Socio actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function show(Socio $socio)
    {
        $socio->load(['comunidad.parroquia.canton','genero','estadoCivil']);
        return view('socios.socio-show', compact('socio'));
    }

    public function destroy(Socio $socio)
    {
        try {
            $socio->delete(); // si usas SoftDeletes, es borrado lógico
            return redirect()->route('socios.index')->with('success', 'Socio eliminado.');
        } catch (\Exception $e) {
            return redirect()->route('socios.index')->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
