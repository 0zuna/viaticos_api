<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Exports\ExportMultiple;
use App\Viaje;
use App\Anticipo;

class ExcelController extends Controller
{
	//
	public function reporte (Request $request) {

		$i= Carbon::parse($request->inicio)->startOfDay()->toDateTimeString();
		$f= Carbon::parse($request->fin)->startOfDay()->toDateTimeString();

		if($request->users[0]=='todos'){
			$data=Viaje::join('users','viajes.user_id','users.id')
			->join('gastos','viajes.id','gastos.viaje_id')
			->selectRaw('viajes.id, viajes.created_at, viajes.area, "C01" as categoria, users.colaborador as name, gastos.motivo as descripcion, viajes.motivo as proveedor, viajes.status,"" as entregado, "" as comision, gastos.costo')
			->whereBetween('viajes.created_at',[$i,$f])
			->get()->toArray();
		}else{
			$data=Viaje::join('users','viajes.user_id','users.id')
			->join('gastos','viajes.id','gastos.viaje_id')
			->selectRaw('viajes.id, viajes.created_at, viajes.area, "C01" as categoria, users.colaborador as name, gastos.motivo as descripcion, viajes.motivo as proveedor, viajes.status, "" as entregado, "" as comision, gastos.costo')
			->whereBetween('viajes.created_at',[$i,$f])
			->whereIn('users.id',$request->users)
			->get()->toArray();
		}

		foreach ($data as $k=>$v) {
			$anticipo=Anticipo::where('viaje_id',$v['id'])->sum('anticipo');
			$s='V-'.str_pad($v['id'], 5, "0", STR_PAD_LEFT);
			$data[$k]['id']=$s;

			if($k==0)
				$data[$k]['entregado']=$anticipo;
			
			else
			if($data[$k-1]['id']!==$data[$k]['id'])
				$data[$k]['entregado']=$anticipo;
			else
				$data[$k]['entregado']='0';
			$data[$k]['comision']='0';
			$data[$k]['saldo']=strval(floatval($data[$k]['entregado'])-$data[$k]['costo']);
			$data[$k]['terogado']=$data[$k]['costo'];
		}

		$data2=\DB::table('expense.solicituds')
			->join('expense.areas','expense.solicituds.area_id','expense.areas.id')
			->join('expense.categorias','expense.solicituds.categoria_id','expense.categorias.id')
			->join('expense.users','expense.solicituds.user_id','expense.users.id')
			->selectRaw('expense.solicituds.id as folio, expense.solicituds.created_at, expense.areas.locacion as area, expense.categorias.codigo, expense.users.name, expense.solicituds.descripcion, expense.solicituds.proveedor_id as proveedor, expense.solicituds.status, expense.solicituds.monto, expense.solicituds.comision, "" as comprobado')
			->whereBetween('expense.solicituds.created_at', [$i,$f])
			->get()->toArray();

		foreach ($data2 as $k=>$v) {
			$data2[$k]=(array)$v;
			$r=\DB::table('expense.gastos')
				->where('expense.gastos.solicitud_id',$v->folio)
				->sum('monto');

			$data2[$k]['comprobado']=strval($r);
			$data2[$k]['saldo']=strval($data2[$k]['monto']-$r);
			$data2[$k]['terogado']=strval($r+$data2[$k]['comision']);
			$proveedor=\DB::table('expense.proveedors')->select('expense.proveedors.nombre')->where('expense.proveedors.id',$data2[$k]['proveedor'])->first();
			$data2[$k]['proveedor']=!empty($proveedor)?$proveedor->nombre:'Efectivo';

			$s='D-'.str_pad($data2[$k]['folio'], 5, "0", STR_PAD_LEFT);
			$data2[$k]['folio']=$s;
		}


		$columns=['Folio', 'Fecha', 'Area', 'Categoría', 'Colaborador', 'Descripción', 'Proveedor', 'Status', 'D.Entregado', 'Comisión','D.Comprobado', 'Saldo', 'T Erogado U'];
		\Excel::store(new ExportMultiple($columns, collect(array_merge((array)$data2,$data))), 'reporte1.xlsx');
		$data = base64_encode(file_get_contents(storage_path('app/reporte1.xlsx')));
		$response =  array(
			'name' => "viajes.xlsx",
			'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64,".$data
		);

		return response()->json($response);
	}
}
