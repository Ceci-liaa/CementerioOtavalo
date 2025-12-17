<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize; // Opcional: para ajustar ancho columnas
use Maatwebsite\Excel\Concerns\WithStyles;    // Opcional: para negritas
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServiciosExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $headings;

    // Constructor para recibir los datos desde el Controller
    public function __construct($data, $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    public function collection()
    {
        return $this->data; // Retorna la colección mapeada
    }

    public function headings(): array
    {
        return $this->headings;
    }

    // Opcional: Poner negrita a la fila 1
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}