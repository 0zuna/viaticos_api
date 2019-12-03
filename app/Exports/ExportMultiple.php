<?php

namespace App\Exports;

use App\Exports\Exporting;
use App\Exports\ExportResumen;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;



class ExportMultiple implements WithMultipleSheets
{
	/**
	* @return \Illuminate\Support\Collection
	*/
	use Exportable;
	public function __construct($columns,$data)
	{
		$this->data = $data;
		$this->columns=$columns;
	}

	public function sheets(): array
	{
		$sheets = [];
		$sheets[] = new Exporting($this->columns, $this->data);
		$sheets[] = new ExportResumen($this->data);
		return $sheets;
	}
}
