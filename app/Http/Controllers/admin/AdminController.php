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
	/*$viajes=User::whereIn('id',$request->users)
		->with(['viajes'=>function($q){
			global $i,$f;
			$q->whereBetween('inicio',[$i,$f])->with(['gastos','anticipos']);
		}])
		->get();*/
	if($request->users[0]=='todos'){
	 $viajes=\DB::table('users')
		//->join('anticipos','users.id','anticipos.user_id')
		->join('viajes','viajes.user_id','users.id')
		//->join('gastos','gastos.viaje_id','viajes.id')
		->select('departamento','colaborador','telefono','viajes.motivo','inicio','fin')
		->selectRaw('(select sum(anticipos.anticipo) from anticipos where viaje_id=viajes.id) as anticipo')
		->selectRaw('(select sum(gastos.costo) from gastos where viaje_id=viajes.id) as gasto')
		->selectRaw('((select sum(anticipos.anticipo) from anticipos where viaje_id=viajes.id)-(select sum(gastos.costo) from gastos where viaje_id=viajes.id)) as diferencia')
		->groupBy('viajes.id','departamento','colaborador','telefono','viajes.motivo','inicio','fin')
		->whereBetween('viajes.inicio',[$i,$f])
		->get();
	}else{
	 $viajes=\DB::table('users')
		//->join('anticipos','users.id','anticipos.user_id')
		->join('viajes','viajes.user_id','users.id')
		//->join('gastos','gastos.viaje_id','viajes.id')
		->select('departamento','colaborador','telefono','viajes.motivo','inicio','fin')
		->selectRaw('(select sum(anticipos.anticipo) from anticipos where viaje_id=viajes.id) as anticipo')
		->selectRaw('(select sum(gastos.costo) from gastos where viaje_id=viajes.id) as gasto')
		->selectRaw('((select sum(anticipos.anticipo) from anticipos where viaje_id=viajes.id)-(select sum(gastos.costo) from gastos where viaje_id=viajes.id)) as diferencia')
		->groupBy('viajes.id','departamento','colaborador','telefono','viajes.motivo','inicio','fin')
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
	$viajes=Viaje::where('user_id',$id)->orderBy('created_at','desc')->with(['anticipos','gastos'])->get();
	return view('admin.viajes',compact('viajes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
	    $gastos=Gasto::select('motivo','created_at','costo')
		    ->where('viaje_id',$request->viaje_id)
		    ->get();
	    $totalGastos=Gasto::select('motivo','created_at','costo')
		    ->where('viaje_id',$request->viaje_id)
		    ->sum('costo');
	    $transporte=Gasto::select('motivo','created_at','costo')
		    ->where('viaje_id',$request->viaje_id)
		    ->where('motivo','Transporte')
		    ->sum('costo');
	    $hospedaje=Gasto::select('motivo','created_at','costo')
		    ->where('viaje_id',$request->viaje_id)
		    ->where('motivo','Hospedaje')
		    ->sum('costo');
	    $comida=Gasto::select('motivo','created_at','costo')
		    ->where('viaje_id',$request->viaje_id)
		    ->where('motivo','Comida')
		    ->sum('costo');
	    $otros=Gasto::select('motivo','created_at','costo')
		    ->where('viaje_id',$request->viaje_id)
		    ->whereNotIn('motivo',['Transporte','Hospedaje','Comida'])
		    ->sum('costo');

	    $viaje=Viaje::find($request->viaje_id);
	    $user=User::find($viaje->user_id);
	    $viaticos=Anticipo::where('viaje_id',$request->viaje_id)->sum('anticipo');


	return Excel::download(new Export($viaje, $gastos, $user, $viaticos, $totalGastos, $transporte, $hospedaje, $comida, $otros), 'viaticos.xlsx');





    }

}
