<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Viaje;
use App\Anticipo;

class ViajeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
	    //$viajes=$request->user()->viajes()->with('gastos')->get()->toarray();
	    $viajes=$request->user()->viajes()
		    ->orderBy('created_at','desc')
		    ->with(['gastos'=>function($q){
			    $q->orderBy('created_at','desc');
	    		},'anticipos']
		    )
		    ->get()->toarray();
	    
	    foreach ($viajes as $k=>$v) {
	    	$gasto_total=array_reduce($v['gastos'],function($v,$w){
			return $v+$w['costo'];
		});
	    	$anticipo=array_reduce($v['anticipos'],function($v,$w){
			return $v+$w['anticipo'];
		});
		$viajes[$k]['anticipo']=number_format($anticipo,2);
		$viajes[$k]['disponible']=number_format($anticipo-$gasto_total,2);
	    }
	    return response()->json($viajes);
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
		$viaje = new Viaje();
		$viaje->motivo=$request->motivo;
		$viaje->inicio=$request->inicio;
		$viaje->fin=$request->fin;
		$viaje->user_id=$request->user()->id;
		$viaje->save();
		return response()->json($viaje, 201);
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
    public function update(Request $request, Viaje $viaje)
    {
        //
	$viaje->motivo=$request->motivo;
	$viaje->anticipo=$request->anticipo;
	$viaje->inicio=$request->inicio;
	$viaje->fin=$request->fin;
	$viaje->update();
	return response()->json($viaje, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Viaje $viaje)
    {
        //
	$viaje->delete();
	return response()->json(null, 204);
    }
    public function finalizar(Request $request, Viaje $viaje)
    {
        //
	$viaje->status='Finalizado';
	$viaje->update();
	return response()->json($viaje, 200);
    }
    public function extendDate(Request $request, Viaje $viaje)
    {
	$viaje=Viaje::find($viaje->id);
	$viaje->fin=$request->fin;
	$viaje->update();
	return response()->json($viaje, 200);
    }
}
