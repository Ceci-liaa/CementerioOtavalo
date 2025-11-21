<?php

// app/Exports/GenericExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericExport implements FromCollection, WithHeadings
{
    protected $data;
    protected $headings;
    protected $relations;  // Pasamos las relaciones que puedan necesitarse

    // Constructor para pasar los datos, los encabezados y las relaciones
    public function __construct($data, $headings, $relations = [])
    {
        $this->data = $data;  // Datos que se van a exportar
        $this->headings = $headings;  // Encabezados de las columnas
        $this->relations = $relations;  // Relaciones que pueden ser necesarias
    }

    // Función para obtener los datos a exportar
    public function collection()
    {
        return $this->data->map(function ($item) {
            $row = [];

            // Iteramos sobre los encabezados y obtenemos los datos correspondientes
            foreach ($this->headings as $heading) {
                switch ($heading) {
                    case 'Código de Usuario':
                        $row[] = $item->codigo_usuario ?? 'N/A';  // Código de Usuario
                        break;
                    case 'Nombre':
                        $row[] = $item->name ?? 'N/A';  // Nombre
                        break;
                    case 'Email':
                        $row[] = $item->email ?? 'N/A';  // Email
                        break;
                    case 'Teléfono':
                        $row[] = $item->phone ?? 'N/A';  // Teléfono, 'N/A' si no tiene
                        break;
                    case 'Ubicación':
                        $row[] = $item->location ?? 'N/A';  // Ubicación, 'N/A' si no tiene
                        break;
                    case 'Rol':
                        $row[] = $item->getRoleNames()->first() ?? 'N/A';  // Rol (primer rol del usuario)
                        break;
                    case 'Estado':
                        $row[] = $item->status ? 'Activo' : 'Inactivo';  // Estado (Activo/Inactivo)
                        break;
                    default:
                        $row[] = 'N/A';  // En caso de que no haya un valor por defecto
                }
            }

            return $row;
        });
    }

    // Función para los encabezados
    public function headings(): array
    {
        return $this->headings;  // Devuelve los encabezados
    }
}
