<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use App\Exports\ViajesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Viaje;
use App\User;
use App\Gasto;
use App\Anticipo;
use App\Exports\Export;

class AdminController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */

	public function __construct()
	{
		$this->middleware('auth');
	}

	public function index(Request $request)
	{
		//
		global $i,$f;
		$i= Carbon::parse($request->inicio)->startOfDay()->toDateTimeString();
		$f= Carbon::parse($request->fin)->startOfDay()->toDateTimeString();
		if($request->users[0]=='todos'){
		$viajes=User::with(['viajes'=>function($q){
				global $i,$f;
				$q->whereBetween('inicio',[$i,$f])->with(['gastos','anticipos']);
			}])
			->get();
		}else{
		$viajes=User::whereIn('id',$request->users)
			->with(['viajes'=>function($q){
				global $i,$f;
				$q->whereBetween('inicio',[$i,$f])->with(['gastos','anticipos']);
			}])
			->get();
			//whereBetween('inicio',[$i,$f])->with(['gastos','anticipos'])->get();
		}
		return response()->json($viajes);
	}

	public function excel(Request $request)
	{
		//
		global $i,$f;
		$i= Carbon::parse($request->inicio)->startOfDay()->toDateTimeString();
		$f= Carbon::parse($request->fin)->startOfDay()->toDateTimeString();
		if($request->users[0]=='todos'){
		 $viajes=\DB::table('users')
			//->join('anticipos','users.id','anticipos.user_id')
			->join('viajes','viajes.user_id','users.id')
			->join('gastos','gastos.viaje_id','viajes.id')
			->select('departamento','colaborador','telefono','viajes.motivo as viaje','gastos.motivo','gastos.costo','inicio','fin')
			->whereBetween('viajes.inicio',[$i,$f])
			->get();
		}else{
		 $viajes=\DB::table('users')
			//->join('anticipos','users.id','anticipos.user_id')
			->join('viajes','viajes.user_id','users.id')
			->join('gastos','gastos.viaje_id','viajes.id')
			->select('departamento','colaborador','telefono','viajes.motivo as viaje','gastos.motivo','gastos.costo','inicio','fin')
			->whereIn('users.id',$request->users)
			->whereBetween('viajes.inicio',[$i,$f])
			->get();
			//whereBetween('inicio',[$i,$f])->with(['gastos','anticipos'])->get();
		}
			return Excel::download(new ViajesExport($viajes),'viajes.xlsx');
			return response()->json($viajes);
	}

	public function users(Request $request)
	{
		//
		if($request->departamento=='all')
			return User::all();
		$users=User::where('departamento',$request->departamento)->get();
		return response()->json($users);
	}

	public function gasto(Request $request)
	{
		//
		$gasto=new Gasto();
		\Log::info($request);
		$gasto->motivo=$request->motivo;
		$gasto->costo=$request->costo;
		$gasto->user_id=$request->user_id;
		$gasto->viaje_id=$request->viaje_id;
		$gasto->save();
		$path = storage_path().'/img/'.$request->user_id.'/viajes/'.$request->viaje_id.'/gastos';
		if(!\File::exists($path)){
			\File::makeDirectory($path, $mode = 0777, true, true);
		}
		if($request->imagen!=''){
			file_put_contents($path.'/'.$gasto->id.".jpg", base64_decode(explode(',',$request->imagen)[1]));
		}
		return response()->json($gasto);
	}

	public function anticipo(Request $request)
	{
		//
		$anticipo=new Anticipo();
		$anticipo->anticipo=$request->anticipo;
		$anticipo->user_id=$request->user_id;
		$anticipo->viaje_id=$request->viaje_id;
		$anticipo->save();
		return response()->json($anticipo);
	}

	public function show($id)
	{
		//
		$viajes=Viaje::where('user_id',$id)->orderBy('created_at','desc')->with(['anticipos','gastos'])->get();
		return view('admin.viajes',compact('viajes'));
	}

	public function deletegasto(Request $request)
	{
		//
		$gasto=Gasto::destroy($request->id);
		return response()->json($gasto);

	}

	public function deleteanticipo(Request $request)
	{
		//
		$anticipo=Anticipo::destroy($request->id);
		return response()->json($anticipo);

	}

	public function deleteviaje(Request $request)
	{
		//
		$viaje=Viaje::findOrFail($request->id);
		$viaje->delete();
		return response()->json($viaje);

	}

	public function excel_viaje(Request $request)
	{
		$gastos=Gasto::select('motivo','created_at','costo')->where('viaje_id',$request->viaje_id)->get();
		$totalGastos=Gasto::select('motivo','created_at','costo')->where('viaje_id',$request->viaje_id)->sum('costo');
		$transporte=Gasto::select('motivo','created_at','costo')->where('viaje_id',$request->viaje_id)->where('motivo','Transporte')->sum('costo');
		$hospedaje=Gasto::select('motivo','created_at','costo')->where('viaje_id',$request->viaje_id)->where('motivo','Hospedaje')->sum('costo');
		$comida=Gasto::select('motivo','created_at','costo')->where('viaje_id',$request->viaje_id)->where('motivo','Comida')->sum('costo');
		$otros=Gasto::select('motivo','created_at','costo')->where('viaje_id',$request->viaje_id)->whereNotIn('motivo',['Transporte','Hospedaje','Comida'])->sum('costo');
		$viaje=Viaje::find($request->viaje_id);
		$user=User::find($viaje->user_id);
		$viaticos=Anticipo::where('viaje_id',$request->viaje_id)->sum('anticipo');
		return Excel::download(new Export($viaje, $gastos, $user, $viaticos, $totalGastos, $transporte, $hospedaje, $comida, $otros), 'viaticos.xlsx');
	}

	public function adeudos (Request $request)
	{
		if($request->users[0]=='todos'){
		$viajes=User::with(['viajes'=>function($q){
				$q->with(['gastos','anticipos']);
			}])
			->get()->toArray();
		}else{
		$viajes=User::whereIn('id',$request->users)
			->with(['viajes'=>function($q){
				$q->with(['gastos','anticipos']);
			}])
			->get()->toArray();
		}
		foreach ($viajes as $k=>$v) {
			foreach ($viajes[$k]['viajes'] as $i=>$vv) {
				$viajes[$k]['viajes'][$i]['anticipoTotal']=array_reduce($vv['anticipos'], function($a,$b)
						{
						return $a+(float)$b['anticipo'];
						},0);
				$viajes[$k]['viajes'][$i]['gastoTotal']=array_reduce($vv['gastos'], function($a,$b)
						{
						return $a+(float)$b['costo'];
						},0);
				$viajes[$k]['viajes'][$i]['adeudo']=$viajes[$k]['viajes'][$i]['anticipoTotal']-$viajes[$k]['viajes'][$i]['gastoTotal'];
			}
			$viajes[$k]['adeudoTotal']=array_reduce($viajes[$k]['viajes'],function($a,$b){return $a+$b['adeudo'];},0);
		}
		return response()->json($viajes);
	}
}
