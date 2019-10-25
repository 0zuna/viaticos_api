<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Input;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Events\BeforeWriting;
use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;



class Export implements ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($viaje, $gastos, $user, $viaticos, $totalGastos, $transporte, $hospedaje, $comida, $otros)
    {
        $this->viaje = $viaje;
	$this->gastos=$gastos;
	$this->user=$user;
	$this->viaticos=$viaticos;
	$this->totalGastos=$totalGastos;
	$this->transporte=$transporte;
	$this->hospedaje=$hospedaje;
	$this->comida=$comida;
	$this->otros=$otros;
    }
    public function registerEvents(): array
    {
      return [
         BeforeExport::class => function(BeforeExport $event){
            $event->writer->reopen(new \Maatwebsite\Excel\Files\LocalTemporaryFile(storage_path('viaticos.xls')),Excel::XLS);
            $event->writer->getSheetByIndex(0);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('D2',$this->user->colaborador);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('E3',$this->viaje->motivo);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('J3',$this->viaje->inicio);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('C6',$this->viaticos);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('C8',$this->totalGastos);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('C10',$this->viaticos-$this->totalGastos);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('H6',$this->transporte);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('H7',$this->hospedaje);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('H8',$this->comida);
            $event->getWriter()->getSheetByIndex(0)->setCellValue('H9',$this->otros);

	    $init=15;
	    foreach ($this->gastos as $gasto) {
            	$event->getWriter()->getSheetByIndex(0)->setCellValue('C'.(String)$init,$gasto->motivo);
            	$event->getWriter()->getSheetByIndex(0)->setCellValue('F'.(String)$init,$gasto->motivo);
            	$event->getWriter()->getSheetByIndex(0)->setCellValue('H'.(String)$init,$gasto->created_at);
            	//$event->getWriter()->getSheetByIndex(0)->setCellValue('I'.(String)$init,'$'.number_format((float)$gasto->costo, 2, '.', ''));
            	//$event->getWriter()->getSheetByIndex(0)->setCellValue('J'.(String)$init,'$'.number_format((float)$gasto->costo, 2, '.', ''));
            	$event->getWriter()->getSheetByIndex(0)->setCellValue('I'.(String)$init,$gasto->costo);
            	$event->getWriter()->getSheetByIndex(0)->setCellValue('J'.(String)$init,$gasto->costo);
		$init++;
	    }
	    $init++;
            $event->getWriter()->getSheetByIndex(0)->setCellValue('C'.(String)$init,'COMPRUEBA: ');
	    $event->getWriter()->getSheetByIndex(0)->getStyle('C'.(String)$init)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDC6D');
	    $init++;
            $event->getWriter()->getSheetByIndex(0)->setCellValue('C'.(String)$init,'NOMBRE Y FIRMA: ');
	    $event->getWriter()->getSheetByIndex(0)->getStyle('C'.(String)$init)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFDC6D');

            return $event->getWriter()->getSheetByIndex(0);
         }
      ];
    }
}
