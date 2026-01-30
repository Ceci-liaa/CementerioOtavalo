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

        $query = Nicho::with('bloque')->orderBy('codigo', 'asc');

        if ($q !== '') {
            $query->where('codigo', 'ILIKE', "%{$q}%");
        }
        if ($bloqueId) {
            $query->where('bloque_id', $bloqueId);
        }

        $nichos = $query->paginate(10)->withQueryString();
        $bloques = Bloque::orderBy('nombre')->get();

        return view('nichos.nicho-index', compact('nichos', 'bloques', 'bloqueId', 'q'));
    }

    public function create()
    {
        $bloques = Bloque::orderBy('nombre', 'asc')->get();
        $socios = Socio::orderBy('apellidos', 'asc')->get();

        // CAMBIO AQUÍ: Agregamos ->sortBy('codigo', SORT_NATURAL) al final
        $nichosGeom = NichoGeom::whereNotIn('id', function ($q) {
            $q->select('nicho_geom_id')
                ->from('nichos')
                ->whereNotNull('nicho_geom_id')
                ->whereNull('deleted_at');
        })
            ->select('id', 'codigo')
            ->get()
            ->sortBy('codigo', SORT_NATURAL); // <--- ESTA ES LA LÍNEA MÁGICA

        return view('nichos.nicho-create', compact('bloques', 'socios', 'nichosGeom'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'bloque_id' => 'required|exists:bloques,id',
            'socio_id' => 'nullable|exists:socios,id',
            'tipo_nicho' => 'required|in:PROPIO,COMPARTIDO',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:disponible,ocupado,mantenimiento',
            'descripcion' => 'nullable|string|max:1000',
            'qr_uuid' => 'nullable|string|unique:nichos,qr_uuid',

            // Validar que el ID del mapa sea único y válido
            'nicho_geom_id' => [
                'nullable',
                'exists:nichos_geom,id',
                Rule::unique('nichos')->whereNull('deleted_at')
            ],
        ]);

        try {
            $data = [
                'bloque_id' => $request->bloque_id,
                'socio_id' => $request->socio_id,
                'nicho_geom_id' => $request->nicho_geom_id, // Guardamos ID mapa
                'tipo_nicho' => $request->tipo_nicho,
                'capacidad' => $request->capacidad,
                'estado' => $request->estado,
                'descripcion' => $request->descripcion,
                // Usamos 'true'/'false' como texto o 1/0 casteado explícitamente si es necesario
                'disponible' => $request->estado === 'disponible' ? 'true' : 'false',
                'qr_uuid' => $request->qr_uuid,
                'created_by' => auth()->id(),
            ];

            // --- LÓGICA DE SINCRONIZACIÓN DE CÓDIGO ---
            if ($request->filled('nicho_geom_id')) {
                $geom = NichoGeom::find($request->nicho_geom_id);
                if ($geom && $geom->codigo) {
                    // Validar duplicado manual
                    if (Nicho::where('codigo', $geom->codigo)->whereNull('deleted_at')->exists()) {
                        return back()->withInput()->with('error', "El código '{$geom->codigo}' ya está registrado.");
                    }
                    // Copiamos el código del mapa (ej: B8-NB97)
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

        // CAMBIO AQUÍ: Agregamos ->sortBy('codigo', SORT_NATURAL) al final
        $nichosGeom = NichoGeom::whereNotIn('id', function ($q) {
            $q->select('nicho_geom_id')
                ->from('nichos')
                ->whereNotNull('nicho_geom_id')
                ->whereNull('deleted_at');
        })
            ->orWhere('id', $nicho->nicho_geom_id)
            ->select('id', 'codigo')
            ->get()
            ->sortBy('codigo', SORT_NATURAL); // <--- ESTA ES LA LÍNEA MÁGICA

        return view('nichos.nicho-edit', compact('nicho', 'bloques', 'socios', 'nichosGeom'));
    }
    public function update(Request $request, Nicho $nicho)
    {
        $request->validate([
            'bloque_id' => 'required|exists:bloques,id',
            'socio_id' => 'nullable|exists:socios,id',
            'tipo_nicho' => 'required|in:PROPIO,COMPARTIDO',
            'capacidad' => 'required|integer|min:1',
            'estado' => 'required|in:disponible,ocupado,mantenimiento',
            'descripcion' => 'nullable|string|max:1000',
            'qr_uuid' => ['nullable', 'string', Rule::unique('nichos')->ignore($nicho->id)],
            'nicho_geom_id' => [
                'nullable',
                'exists:nichos_geom,id',
                Rule::unique('nichos')->whereNull('deleted_at')->ignore($nicho->id)
            ],
        ]);

        try {
            $data = [
                'bloque_id' => $request->bloque_id,
                'socio_id' => $request->socio_id,
                'nicho_geom_id' => $request->nicho_geom_id,
                'tipo_nicho' => $request->tipo_nicho,
                'capacidad' => $request->capacidad,
                'estado' => $request->estado,
                'descripcion' => $request->descripcion,

                // --- AQUÍ ESTÁ LA CORRECCIÓN ---
                // Si el estado NO es 'disponible' (es ocupado o mantenimiento), 
                // forzamos a que 'disponible' sea 'false'.
                'disponible' => $request->estado === 'disponible' ? 'true' : 'false',

                'qr_uuid' => $request->qr_uuid,
            ];

            // Si el usuario cambia el código manualmente
            if ($request->filled('codigo')) {
                $data['codigo'] = $request->codigo;
            }

            // Si cambió el mapa, actualizamos el código automáticamente
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

        // AQUÍ ESTÁ LA CORRECCIÓN DE ORDENAMIENTO:
        // En lugar de "orderBy" de SQL (que mezcla B1, B10, B2),
        // usamos "get()" y luego "sortBy(..., SORT_NATURAL)".
        $nichos = Nicho::with('bloque')
            ->whereIn('id', $ids)
            ->get() // 1. Traemos los datos de la BD
            ->sortBy('codigo', SORT_NATURAL); // 2. Ordenamos inteligente (B-2 antes que B-10)

        // Encabezados
        $headings = [
            'ID',
            'Código',
            'Bloque',
            'Estado',
            'Disponibilidad',
            'Capacidad'
        ];

        // Mapeo de datos (Tu lógica original intacta)
        $data = $nichos->map(function ($n) {
            return [
                'id' => $n->id,
                'codigo' => $n->codigo,
                'bloque' => $n->bloque->nombre ?? 'N/A',
                'estado' => ucfirst($n->estado),
                'disponibilidad' => $n->disponible ? 'Sí' : 'No',
                'capacidad' => $n->capacidad,
            ];
        });

        if ($reportType === 'excel') {
            return Excel::download(new NichosExport($data, $headings), 'nichos_reporte.xlsx');

        } elseif ($reportType === 'pdf') {
            $pdf = Pdf::loadView('nichos.reports-pdf', compact('data', 'headings'));
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('nichos_reporte_' . date('YmdHis') . '.pdf');
        }

        return redirect()->back();
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