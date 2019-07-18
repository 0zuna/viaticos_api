<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ViajesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($viajes)
    {
        $this->viajes = $viajes;
    }
    public function collection()
    {
        return $this->viajes;
    }
     public function headings(): array
    {
        return [
            'DEPARTAMENTO',
            'NOMBRE',
            'TELEFONO',
            'VIAJE',
            'FECHA INICIO',
            'FECHA FIN',
            'ANTICIPO',
	    'GASTO',
	    'DIFERENCIA'
        ];
    }
}
