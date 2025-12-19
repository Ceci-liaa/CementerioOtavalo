<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\FacturaDetalle;
use App\Models\Socio;
use App\Models\Beneficio;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Librería del PDF

class FacturaController extends Controller
{
    /**
     * Muestra el listado de facturas con filtros.
     */
    public function index(Request $request)
    {
        // 1. Limpiamos la búsqueda
        $q = trim($request->get('q', ''));

        // 2. Consulta base ordenando por fecha y luego por ID descendente
        $query = Factura::with('socio')->orderByDesc('fecha')->orderByDesc('id');

        // 3. Aplicamos filtros si existe búsqueda
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('cliente_nombre', 'ILIKE', "%{$q}%")
                    ->orWhere('cliente_apellido', 'ILIKE', "%{$q}%")
                    ->orWhere('cliente_cedula', 'ILIKE', "%{$q}%")
                    ->orWhere('codigo', 'ILIKE', "%{$q}%") // Búsqueda también por código
                    ->orWhereHas('socio', function ($s) use ($q) {
                        $s->where('nombres', 'ILIKE', "%{$q}%")
                            ->orWhere('apellidos', 'ILIKE', "%{$q}%")
                            ->orWhere('cedula', 'ILIKE', "%{$q}%");
                    });
            });
        }

        // 4. Paginación
        $facturas = $query->paginate(10)->withQueryString();

        return view('facturas.factura-index', compact('facturas', 'q'));
    }

    /**
     * Muestra el formulario de creación (Modal).
     */
    public function create()
    {
        $socios = Socio::orderBy('apellidos')->orderBy('nombres')
            ->get(['id', 'nombres', 'apellidos', 'cedula', 'telefono', 'email']);
        
        $beneficios = Beneficio::orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();

        return view('facturas.factura-create', compact('socios', 'beneficios', 'servicios'));
    }

    /**
     * Guarda la factura inicial como BORRADOR.
     */
    public function store(Request $request)
    {
        // 1. Validaciones Completas
        $request->validate([
            'socio_id'          => 'nullable|exists:socios,id',
            'cliente_nombre'    => 'required|string|max:255',
            'cliente_apellido'  => 'nullable|string|max:255',
            'cliente_cedula'    => 'nullable|string|max:20',
            'cliente_email'     => 'nullable|string|max:255',
            'cliente_telefono'  => 'nullable|string|max:30',
            'fecha'             => 'required|date',
            // Validación de Arrays de Ítems
            'items.cantidad'       => 'required|array|min:1',
            'items.beneficio_id.*' => 'nullable|exists:beneficios,id',
            'items.servicio_id.*'  => 'nullable|exists:servicios,id',
            'items.cantidad.*'     => 'required|integer|min:1',
            'items.precio.*'       => 'required|numeric|min:0',
        ]);

        // 2. Procesamiento de Ítems (Filtrar vacíos y calcular subtotales)
        $itemsProcesados = [];
        $rawBeneficios = $request->input('items.beneficio_id', []);
        $rawServicios  = $request->input('items.servicio_id', []);
        $rawCantidades = $request->input('items.cantidad', []);
        $rawPrecios    = $request->input('items.precio', []);

        foreach ($rawCantidades as $i => $cant) {
            $cantidad = (int) $cant;
            $precio = (float) ($rawPrecios[$i] ?? 0);
            $beneficioId = $rawBeneficios[$i] ?? null;
            $servicioId = $rawServicios[$i] ?? null;

            // Ignorar si cantidad es 0 o no hay ID seleccionado
            if ($cantidad <= 0 || (!$beneficioId && !$servicioId)) {
                continue;
            }
            
            // Validación lógica de negocio: No puede ser Beneficio y Servicio a la vez
            if ($beneficioId && $servicioId) {
                return back()->withInput()->with('error', 'Error en fila: Selecciona beneficio O servicio, no ambos.');
            }

            $itemsProcesados[] = [
                'beneficio_id' => $beneficioId,
                'servicio_id'  => $servicioId,
                'cantidad'     => $cantidad,
                'precio'       => $precio,
                'subtotal'     => $cantidad * $precio,
            ];
        }

        if (count($itemsProcesados) === 0) {
            return back()->withInput()->with('error', 'Debes agregar al menos un ítem válido.');
        }

        // 3. Transacción de Base de Datos
        DB::beginTransaction();
        try {
            // Lógica de Snapshot: Si seleccionó socio, aseguramos guardar sus datos
            if ($request->filled('socio_id')) {
                $socio = Socio::find($request->socio_id);
                if ($socio) {
                    $request->merge([
                        'cliente_nombre'   => $request->cliente_nombre ?: $socio->nombres,
                        'cliente_apellido' => $request->cliente_apellido ?: $socio->apellidos,
                        'cliente_cedula'   => $request->cliente_cedula ?: $socio->cedula,
                        'cliente_email'    => $request->cliente_email ?: $socio->email,
                        'cliente_telefono' => $request->cliente_telefono ?: $socio->telefono,
                    ]);
                }
            }

            // Cálculos finales
            $total = array_sum(array_column($itemsProcesados, 'subtotal'));
            $codigoGenerado = $this->generarCodigoUnico(); 

            // Crear Cabecera
            $factura = Factura::create([
                'codigo'           => $codigoGenerado,
                'socio_id'         => $request->socio_id,
                'cliente_nombre'   => $request->cliente_nombre,
                'cliente_apellido' => $request->cliente_apellido,
                'cliente_cedula'   => $request->cliente_cedula,
                'cliente_email'    => $request->cliente_email,
                'cliente_telefono' => $request->cliente_telefono,
                'fecha'            => $request->fecha,
                'total'            => $total,
                'estado'           => 'BORRADOR', // <--- IMPORTANTE: Nace editable
            ]);

            // Crear Detalles
            foreach ($itemsProcesados as $it) {
                $it['factura_id'] = $factura->id;
                FacturaDetalle::create($it);
            }

            DB::commit();

            // 4. Redirección al Index (No al Show)
            return redirect()->route('facturas.index')
                ->with('success', "Factura $codigoGenerado guardada como BORRADOR. Puede editarla o emitirla.");
                
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error grave al crear: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el detalle de la factura.
     */
    public function show(Factura $factura)
    {
        $factura->load(['socio','detalles.beneficio','detalles.servicio']);
        return view('facturas.factura-show', compact('factura'));
    }

    /**
     * Formulario de edición (Solo Borradores).
     */
    /**
     * Formulario de edición (Solo Borradores).
     */
    public function edit(Factura $factura)
    {
        if ($factura->estado !== 'BORRADOR') {
            return back()->with('error', 'No se puede editar una factura que ya fue emitida o anulada.');
        }

        // --- CORRECCIÓN: Cargar catálogos y detalles ---
        $socios = Socio::orderBy('apellidos')->get(); // Opcional si permites cambiar socio
        $beneficios = Beneficio::orderBy('nombre')->get();
        $servicios = Servicio::orderBy('nombre')->get();
        
        // Cargar los detalles para pintarlos en la tabla
        $factura->load('detalles');

        return view('facturas.factura-edit', compact('factura', 'socios', 'beneficios', 'servicios'));
    }

    /**
     * Actualiza datos del cliente Y LOS ÍTEMS (Solo Borradores).
     */
    public function update(Request $request, Factura $factura)
    {
        // 1. Bloqueo de seguridad
        if ($factura->estado !== 'BORRADOR') {
             return back()->with('error', 'Factura bloqueada. No se permiten cambios.');
        }

        // 2. Validaciones completas (Cliente + Items)
        $request->validate([
            'cliente_nombre'   => 'required|string|max:255',
            'cliente_apellido' => 'nullable|string|max:255',
            'cliente_cedula'   => 'nullable|string|max:20',
            'cliente_email'    => 'nullable|string|max:255',
            'cliente_telefono' => 'nullable|string|max:30',
            'fecha'            => 'required|date',
            // Validar items igual que en store
            'items.cantidad'       => 'required|array|min:1',
            'items.beneficio_id.*' => 'nullable|exists:beneficios,id',
            'items.servicio_id.*'  => 'nullable|exists:servicios,id',
            'items.cantidad.*'     => 'required|integer|min:1',
            'items.precio.*'       => 'required|numeric|min:0',
        ]);

        // 3. Procesar Items (Lógica idéntica al store)
        $itemsProcesados = [];
        $rawBeneficios = $request->input('items.beneficio_id', []);
        $rawServicios  = $request->input('items.servicio_id', []);
        $rawCantidades = $request->input('items.cantidad', []);
        $rawPrecios    = $request->input('items.precio', []);

        foreach ($rawCantidades as $i => $cant) {
            $cantidad = (int) $cant;
            $precio = (float) ($rawPrecios[$i] ?? 0);
            $beneficioId = $rawBeneficios[$i] ?? null;
            $servicioId = $rawServicios[$i] ?? null;

            if ($cantidad <= 0 || (!$beneficioId && !$servicioId)) continue;

            $itemsProcesados[] = [
                'beneficio_id' => $beneficioId,
                'servicio_id'  => $servicioId,
                'cantidad'     => $cantidad,
                'precio'       => $precio,
                'subtotal'     => $cantidad * $precio,
            ];
        }

        if (empty($itemsProcesados)) {
            return back()->withInput()->with('error', 'La factura debe tener al menos un ítem.');
        }

        DB::beginTransaction();
        try {
            // 4. Calcular nuevo total
            $total = array_sum(array_column($itemsProcesados, 'subtotal'));

            // 5. Actualizar Cabecera
            $factura->update([
                'cliente_nombre'   => $request->cliente_nombre,
                'cliente_apellido' => $request->cliente_apellido,
                'cliente_cedula'   => $request->cliente_cedula,
                'cliente_email'    => $request->cliente_email,
                'cliente_telefono' => $request->cliente_telefono,
                'fecha'            => $request->fecha,
                'total'            => $total, // Actualizamos el total
                // El estado sigue siendo BORRADOR
            ]);

            // 6. Actualizar Detalles (Estrategia: Borrar todo y volver a crear)
            $factura->detalles()->delete();

            foreach ($itemsProcesados as $item) {
                $item['factura_id'] = $factura->id;
                FacturaDetalle::create($item);
            }

            DB::commit();
            return redirect()->route('facturas.index')->with('success', 'Factura actualizada correctamente.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
    /**
     * ELIMINAR BLOQUEADO: Obligamos a usar Anular para mantener secuencia.
     */
    public function destroy(Factura $factura)
    {
        // Política estricta: No borrar para no perder el consecutivo (FAC-XXXX)
        // Incluso si es borrador, es mejor dejarla como ANULADA para auditoría.
        return back()->with('error', 'Por seguridad y auditoría, las facturas no se deben eliminar. Utilice la opción ANULAR.');
    }

    /**
     * Acción: EMITIR (Finalizar)
     * Cambia el estado a EMITIDA y bloquea edición.
     */
    public function emitir(Factura $factura)
    {
        if ($factura->estado !== 'BORRADOR') {
            return back()->with('error', 'Solo se pueden emitir facturas que estén en estado BORRADOR.');
        }
        
        $factura->update([
            'estado'      => 'EMITIDA',
            'emitida_at'  => now(),
            'emitida_por' => auth()->id() ?? null
        ]);
        
        return redirect()->route('facturas.index')
            ->with('success', "Factura {$factura->codigo} emitida correctamente. Ya está disponible el PDF.");
    }

    /**
     * Acción: ANULAR
     * Requiere un motivo y cambia el estado a ANULADA.
     */
    public function anular(Request $request, Factura $factura)
    {
        // Validación obligatoria del motivo
        $request->validate([
            'motivo' => 'required|string|min:5|max:500'
        ], [
            'motivo.required' => 'Debe especificar el motivo de la anulación.',
            'motivo.min'      => 'El motivo debe ser más descriptivo.'
        ]);

        if ($factura->estado === 'ANULADA') {
            return back()->with('error', 'Esta factura ya se encuentra anulada.');
        }

        // Actualizamos estado y guardamos auditoría
        $factura->update([
            'estado'           => 'ANULADA',
            'anulada_at'       => now(),
            'anulada_por'      => auth()->id() ?? null,
            'motivo_anulacion' => $request->input('motivo')
        ]);

        return redirect()->route('facturas.index')
            ->with('success', "La Factura {$factura->codigo} ha sido ANULADA.");
    }

    /**
     * Acción: GENERAR PDF (Mágica)
     * Si es borrador, la emite automáticamente.
     */
    public function generarPdf(Factura $factura)
    {
        // 1. Lógica de Auto-Emisión
        if ($factura->estado === 'BORRADOR') {
            $factura->update([
                'estado'      => 'EMITIDA',
                'emitida_at'  => now(),
                'emitida_por' => auth()->id() ?? null
            ]);
        }
        
        // 2. Preparar datos para la vista
        $factura->load(['detalles', 'socio']);
        
        // 3. Generar PDF
        $pdf = Pdf::loadView('facturas.factura-pdf', compact('factura'));

        // 4. Descargar PDF
        return $pdf->download("Factura-{$factura->codigo}.pdf");
    }

    /**
     * Helper privado para generar código único consecutivo.
     */
    private function generarCodigoUnico()
    {
        $ultimaFactura = Factura::latest('id')->first();

        if (!$ultimaFactura) {
            return 'FAC-00001';
        }

        // Extraer números (FAC-00045 -> 45)
        $numeroUltimo = intval(preg_replace('/[^0-9]/', '', $ultimaFactura->codigo));
        
        // Sumar 1
        $nuevoNumero = $numeroUltimo + 1;
        
        // Formatear (FAC-00046)
        return 'FAC-' . str_pad($nuevoNumero, 5, '0', STR_PAD_LEFT);
    }
}