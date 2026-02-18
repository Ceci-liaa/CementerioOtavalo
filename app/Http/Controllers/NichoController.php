<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Nicho;
use App\Models\Bloque;
use App\Models\Socio;
use App\Models\NichoGeom;

// Librerías Reportes
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NichosExport;
use Illuminate\Support\Facades\Http;

class NichoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $bloqueId = $request->get('bloque_id');
        $estado = $request->get('estado');

        $query = Nicho::with(['bloque', 'socio'])->orderBy('codigo', 'asc');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('codigo', 'ILIKE', "%{$q}%")
                    ->orWhereHas('socio', function ($qs) use ($q) {
                        $qs->where('apellidos', 'ILIKE', "%{$q}%")
                           ->orWhere('nombres', 'ILIKE', "%{$q}%");
                    });
            });
        }
        if ($bloqueId) {
            $query->where('bloque_id', $bloqueId);
        }
        if ($estado) {
            $query->where('estado', $estado);
        }

        $nichos = $query->paginate(10)->withQueryString();
        $bloques = Bloque::orderBy('nombre')->get();

        return view('nichos.nicho-index', compact('nichos', 'bloques', 'bloqueId', 'q'));
    }
    public function create()
    {
        $bloques = Bloque::orderBy('nombre', 'asc')->get();
        $socios = Socio::orderBy('apellidos', 'asc')->get();

        $nichosGeom = NichoGeom::whereNotIn('id', function ($q) {
            $q->select('nicho_geom_id')->from('nichos')->whereNotNull('nicho_geom_id')->whereNull('deleted_at');
        })
            ->select('id', 'codigo')->get()->sortBy('codigo', SORT_NATURAL);

        return view('nichos.nicho-create', compact('bloques', 'socios', 'nichosGeom'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bloque_id' => 'required|exists:bloques,id',
            'socio_id' => 'nullable|exists:socios,id',
            'tipo_nicho' => 'required|in:PROPIO,COMPARTIDO',
            // NUEVO CAMPO: CLASE
            'clase_nicho' => 'required|in:BOVEDA,TIERRA',
            'capacidad' => 'required|integer|min:1',
            // ESTADO FÍSICO (Nuevos valores)
            'estado' => 'required|in:BUENO,MANTENIMIENTO,MALO,ABANDONADO',
            'descripcion' => 'nullable|string|max:1000',
            'qr_uuid' => 'nullable|string|unique:nichos,qr_uuid',
            'nicho_geom_id' => ['nullable', 'exists:nichos_geom,id', Rule::unique('nichos')->whereNull('deleted_at')],
        ]);

        try {
            $data = $request->all();
            $data['created_by'] = auth()->id();

            // Lógica de Disponibilidad Inicial:
            // Si se crea nuevo, asumimos que 'ocupacion' es 0 y 'disponible' es true.
            // (El usuario no llena 'ocupacion' manualmente en el formulario).
            $data['ocupacion'] = 0;
            $data['disponible'] = true;

            // Sincronización de código con mapa
            if ($request->filled('nicho_geom_id')) {
                $geom = NichoGeom::find($request->nicho_geom_id);
                if ($geom && $geom->codigo) {
                    if (Nicho::where('codigo', $geom->codigo)->whereNull('deleted_at')->exists()) {
                        return back()->withInput()->with('error', "El código '{$geom->codigo}' ya está registrado.");
                    }
                    $data['codigo'] = $geom->codigo;
                }
            }

            Nicho::create($data);

            return redirect()->route('nichos.index')->with('success', 'Nicho creado correctamente.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show(Nicho $nicho)
    {
        $nicho->load(['bloque', 'nichoGeom']);
        return view('nichos.nicho-show', compact('nicho'));
    }

    public function edit(Nicho $nicho)
    {
        $bloques = Bloque::orderBy('nombre', 'asc')->get();
        $socios = Socio::orderBy('apellidos', 'asc')->get();

        $nichosGeom = NichoGeom::whereNotIn('id', function ($q) {
            $q->select('nicho_geom_id')->from('nichos')->whereNotNull('nicho_geom_id')->whereNull('deleted_at');
        })
            ->orWhere('id', $nicho->nicho_geom_id)
            ->select('id', 'codigo')->get()->sortBy('codigo', SORT_NATURAL);

        return view('nichos.nicho-edit', compact('nicho', 'bloques', 'socios', 'nichosGeom'));
    }

    public function update(Request $request, Nicho $nicho)
    {
        $request->validate([
            'bloque_id' => 'required|exists:bloques,id',
            'socio_id' => 'nullable|exists:socios,id',
            'tipo_nicho' => 'required|in:PROPIO,COMPARTIDO',
            'clase_nicho' => 'required|in:BOVEDA,TIERRA', // VALIDACIÓN NUEVA
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:BUENO,MANTENIMIENTO,MALO,ABANDONADO', // VALORES NUEVOS
            'descripcion' => 'nullable|string|max:1000',
            'qr_uuid' => ['nullable', 'string', Rule::unique('nichos')->ignore($nicho->id)],
            'nicho_geom_id' => ['nullable', 'exists:nichos_geom,id', Rule::unique('nichos')->whereNull('deleted_at')->ignore($nicho->id)],
        ]);

        try {
            $data = $request->except(['ocupacion', 'disponible']); // Protegemos la lógica de ocupación

            if ($request->filled('codigo')) {
                $data['codigo'] = $request->codigo;
            }

            if ($request->filled('nicho_geom_id') && $request->nicho_geom_id != $nicho->nicho_geom_id) {
                $geom = NichoGeom::find($request->nicho_geom_id);
                if ($geom && $geom->codigo) {
                    if (Nicho::where('codigo', $geom->codigo)->where('id', '!=', $nicho->id)->whereNull('deleted_at')->exists()) {
                        return back()->withInput()->with('error', "El código '{$geom->codigo}' ya está en uso.");
                    }
                    $data['codigo'] = $geom->codigo;
                }
            }

            $nicho->update($data);

            return redirect()->route('nichos.index')->with('success', 'Nicho actualizado.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function destroy(Nicho $nicho)
    {
        try {
            $nicho->delete();
            return redirect()->route('nichos.index')->with('success', 'Nicho eliminado.');
        } catch (\Throwable $e) {
            return redirect()->route('nichos.index')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // ── REPORTES PDF Y EXCEL ───────────────────────────────────────
    public function reports(Request $request)
    {
        $ids = $request->input('ids', []);
        $reportType = $request->input('report_type');

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Debe seleccionar al menos un registro.');
        }

        $nichos = Nicho::with('bloque')->whereIn('id', $ids)->get()->sortBy('codigo', SORT_NATURAL);

        // NUEVOS ENCABEZADOS
        $headings = [
            'ID',
            'Código',
            'Bloque',
            'Clase',
            'Tipo',
            'Estado Físico',
            'Ocupación'
        ];

        $data = $nichos->map(function ($n) {
            return [
                'id' => $n->id,
                'codigo' => $n->codigo,
                'bloque' => $n->bloque->nombre ?? 'N/A',
                'clase' => $n->clase_nicho, // NUEVO
                'tipo' => $n->tipo_nicho,
                'estado' => ucfirst(strtolower($n->estado)), // Físico
                'ocupacion' => $n->ocupacion . ' / ' . $n->capacidad, // "1 / 3"
            ];
        });

        if ($reportType === 'excel') {
            return Excel::download(new NichosExport($data, $headings), 'nichos.xlsx');
        } elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('nichos.reports-pdf', compact('data', 'headings'));
            return $pdf->download('nichos-'. date('YmdHis') . '.pdf');
        }
    }
    // Agrega esto en tu controlador

    // ─── 1. VISTA DE PREVISUALIZACIÓN (Ticket) ───
    public function downloadQr(Request $request, Nicho $nicho)
    {
        $mode = $request->get('mode', 'text');

        // Cargamos relaciones
        $nicho->load(['bloque', 'fallecidos', 'socios']);

        $textoQR = "";
        $titulo = "";

        if ($mode === 'url') {
            // ---------------------------------------------------------
            // OPCIÓN FUTURA (ONLINE) - MANTENER COMENTADO
            // ---------------------------------------------------------
            // Esta línea se descomentará cuando tengas la ruta pública lista:
            // $textoQR = route('public.nicho.info', ['uuid' => $nicho->qr_uuid]);

            // POR AHORA: Usamos un link genérico para que no de error
            $textoQR = url('/ver-nicho/' . $nicho->qr_uuid);

            $titulo = "QR WEB (FUTURO - EN CONSTRUCCIÓN)";

        } else {
            // ---------------------------------------------------------
            // OPCIÓN PRESENTE (OFFLINE / TEXTO)
            // ---------------------------------------------------------
            $textoQR = "NICHO: " . $nicho->codigo . "\n";
            $textoQR .= "BLOQUE: " . ($nicho->bloque->nombre ?? 'S/N') . "\n";

            if ($nicho->fallecidos->isNotEmpty()) {
                $textoQR .= "\n--- OCUPANTES ---\n";
                foreach ($nicho->fallecidos as $f) {
                    $textoQR .= "- " . $f->apellidos . " " . $f->nombres . "\n";
                }
            } else {
                $textoQR .= "\nESTADO: " . ucfirst($nicho->estado);
            }

            if ($nicho->socios->isNotEmpty()) {
                $responsable = $nicho->socios->first();
                $textoQR .= "\n--- RESPONSABLE ---\n";
                $textoQR .= $responsable->apellidos . " " . $responsable->nombres;
            }

            $titulo = "QR DE DATOS (OFFLINE)";
        }

        // Generamos la URL de la imagen para la vista
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($textoQR);

        // Retornamos la vista de previsualización
        return view('nichos.nicho-qr-print', compact('nicho', 'qrUrl', 'textoQR', 'titulo', 'mode'));
    }

    // ─── 2. DESCARGA DIRECTA DE IMAGEN PNG (Acción del botón verde) ───
    public function downloadQrImage(Nicho $nicho)
    {
        // 1. Reconstruimos el texto (Usamos la lógica OFFLINE por defecto para la descarga directa)
        $nicho->load(['bloque', 'fallecidos', 'socios']);

        $texto = "NICHO: " . $nicho->codigo . "\n";
        $texto .= "BLOQUE: " . ($nicho->bloque->nombre ?? 'S/N') . "\n";

        if ($nicho->fallecidos->isNotEmpty()) {
            $texto .= "\n--- OCUPANTES ---\n";
            foreach ($nicho->fallecidos as $f) {
                $texto .= $f->apellidos . " " . $f->nombres . "\n";
            }
        } else {
            $texto .= "\nESTADO: " . ucfirst($nicho->estado);
        }

        if ($nicho->socios->isNotEmpty()) {
            $r = $nicho->socios->first();
            $texto .= "\n--- RESPONSABLE ---\n";
            $texto .= $r->apellidos . " " . $r->nombres;
        }

        // 2. Pedimos la imagen a la API (Tamaño 500x500 para mejor calidad)
        $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=500x500&margin=10&data=" . urlencode($texto);

        // 3. Obtenemos el contenido del archivo
        $imageContent = Http::get($apiUrl)->body();

        // 4. Forzamos la descarga del archivo .png
        $filename = 'QR_' . $nicho->codigo . '.png';

        return response($imageContent)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    // Muestra la información al escanear el QR (Vista para celular)
    public function publicShow($uuid)
    {
        $nicho = Nicho::with('bloque')->where('qr_uuid', $uuid)->firstOrFail();
        // Retorna una vista simple pública
        return view('nichos.public-info', compact('nicho'));
    }
}