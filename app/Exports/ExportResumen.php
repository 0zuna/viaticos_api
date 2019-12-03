<?php

namespace App\Exports;

use App\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportResumen implements WithTitle, ShouldAutoSize, WithEvents
{
	/**
	* @return \Illuminate\Support\Collection
	*/
	public function __construct($data)
	{
		$this->data = $data;
	}

	public function title(): string
	{
		return 'Resumen';
	}

	public function saldo($event)
	{
		$event->sheet->setCellValue('A1',"Colaborador");
		$event->sheet->setCellValue('B1',"Saldo");

		$users=$this->data->groupBy('name')->toArray();
		$row=2;
		foreach ($users as $value) {
			$sum=array_reduce($value,function($a,$b){
				return $a+=$b['saldo'];
			},0);

			$event->sheet->setCellValue('A'.$row, $value[0]['name']);
			$event->sheet->setCellValue('B'.$row, $sum);
			$row++;
		}
	}

	public function status($event){

		$event->sheet->setCellValue('D1',"Colaborador");
		$event->sheet->setCellValue('E1',"Status");
		$event->sheet->setCellValue('F1',"Contador");

		$users=$this->data->groupBy('name')->toArray();
		$row=2;
		foreach ($users as $value) {
			$status=collect($value)->countBy(function($a){return $a['status'];})->toArray();
			foreach ($status as $k=>$v) {
				$event->sheet->setCellValue('D'.$row, $value[0]['name']);
				$event->sheet->setCellValue('E'.$row, $k);
				$event->sheet->setCellValue('F'.$row, $v);
				$row++;
			}
		}
	}

	public function areaCategoria($event){
		$event->sheet->setCellValue('H1',"Área");
		$event->sheet->setCellValue('I1',"Categoría");
		$event->sheet->setCellValue('J1',"T.Erogado");
		$areas=$this->data->groupBy('area')->toArray();
		$row=2;
		foreach ($areas as $k=>$v){
			$descripcions=collect($v)->groupBy('descripcion');
			foreach ($descripcions as $kk=>$vv) {
				$event->sheet->setCellValue('H'.$row, $k);
				$event->sheet->setCellValue('I'.$row, $kk);
				$event->sheet->setCellValue('J'.$row, $vv->sum('terogado'));
				$row++;
			}
		}
	}

	public function area($event){
		$event->sheet->setCellValue('L1',"Área");
		$event->sheet->setCellValue('M1',"T.Erogado");
		$areas=$this->data->groupBy('area');
		$row=2;
		foreach ($areas as $k=>$v) {
			$event->sheet->setCellValue('L'.$row, $k);
			$event->sheet->setCellValue('M'.$row, $v->sum('terogado'));
			$row++;
		}
	}

	public function categoria($event){
		$event->sheet->setCellValue('O1',"Categoría");
		$event->sheet->setCellValue('P1',"T.Erogado");
		$areas=$this->data->groupBy('descripcion');
		$row=2;
		foreach ($areas as $k=>$v) {
			$event->sheet->setCellValue('O'.$row, $k);
			$event->sheet->setCellValue('P'.$row, $v->sum('terogado'));
			$row++;
		}
	}

	public function registerEvents(): array
	{

		return [
			AfterSheet::class=> function(AfterSheet $event) {
				$this->saldo($event);
				$this->status($event);
				$this->areaCategoria($event);
				$this->area($event);
				$this->categoria($event);
			},
		];
	}
}
