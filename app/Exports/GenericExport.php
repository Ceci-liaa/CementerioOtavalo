<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $headings;
    protected $relations;

    public function __construct($data, $headings, $relations = [])
    {
        $this->data = $data;
        $this->headings = $headings;
        $this->relations = $relations;
    }

    public function collection()
    {
        // 🚨 SOLUCIÓN DEFINITIVA: 
        // Si el controlador ya nos envió una colección de arrays asociativos (datos planos, como Parroquias),
        // simplemente la devolvemos. Si el controlador nos envía una colección de modelos (como Usuarios),
        // debemos mapearla aquí para asegurar que los métodos complejos se resuelvan correctamente.

        // 1. Convertir a Collection si es necesario (ej: si $this->data es un array)
        $collection = $this->data instanceof \Illuminate\Support\Collection ? $this->data : collect($this->data);
        
        // 2. Comprobar si los datos YA están mapeados (ej: Parroquias)
        // Si el primer elemento es un array, asumimos que ya está mapeado y listo para exportar.
        if (isset($collection[0]) && is_array($collection[0])) {
            return $collection;
        }

        // 3. Si llega aquí, significa que tenemos una Colección de OBJETOS MODELOS (ej: Usuarios)
        // Mapeamos los objetos a un array plano aquí, como lo hacía el GenericExport original, 
        // pero solo para los modelos.
        return $collection->map(function ($item) {
            $row = [];

            // Este mapeo SOLO se ejecuta si son modelos Eloquent que necesitan resolver propiedades y métodos.
            // Para Usuarios, asumimos que $item es el modelo User.

            foreach ($this->headings as $heading) {
                switch ($heading) {
                    // --- CASOS PARA USUARIOS (Modelo User) ---
                    case 'Código de Usuario':
                        $row[] = $item->codigo_usuario ?? 'N/A';
                        break;
                    case 'Nombre':
                        $row[] = $item->name ?? 'N/A';
                        break;
                    case 'Email':
                        $row[] = $item->email ?? 'N/A';
                        break;
                    case 'Teléfono':
                        $row[] = $item->phone ?? 'N/A';
                        break;
                    case 'Ubicación':
                        $row[] = $item->location ?? 'N/A';
                        break;
                    case 'Rol':
                        $row[] = $item->getRoleNames()?->first() ?? 'N/A';
                        break;
                    case 'Estado':
                        $row[] = $item->status ? 'Activo' : 'Inactivo';
                        break;
                    // --- FIN CASOS USUARIOS ---
                    
                    // --- SI QUIERES EXPORTAR PARROQUIAS SIN MAPEAR PREVIAMENTE (Ahora esto es redundante, pero lo mantenemos) ---
                    case 'Código':
                        $row[] = $item->codigo ?? 'N/A';
                        break;
                    case 'Parroquia':
                        $row[] = $item->nombre ?? 'N/A';
                        break;
                    case 'Cantón':
                        $row[] = $item->canton?->nombre ?? 'Sin Cantón';
                        break;
                    case 'Fecha Registro': // Este campo solo funciona si es un modelo y no se quitó en el controlador
                        $row[] = $item->created_at?->format('d/m/Y') ?? '';
                        break;

                    // Default
                    default:
                        // Acceso dinámico si la propiedad existe en el modelo (fallback seguro)
                        $key = lcfirst(str_replace(' ', '', $heading));
                        $row[] = $item->{$key} ?? 'N/A';
                }
            }
            return $row;
        });

    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}