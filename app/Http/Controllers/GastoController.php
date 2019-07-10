<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gasto;
use App\Viaje;

class GastoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
	    return response()->json($request->user()->gastos()->get());
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
		$gasto = new Gasto();
		$gasto->costo=$request->costo;
		$gasto->motivo=$request->motivo;
		$gasto->viaje_id=$request->viaje_id;
		$gasto->user_id=$request->user()->id;
		$gasto->save();
		$sum=Gasto::where('viaje_id',$request->viaje_id)->sum('costo');
		$viaje=Viaje::find($request->viaje_id);
		return response()->json(['gasto'=>$gasto,'disponible'=>$viaje->anticipo-$sum], 201);
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
	$gasto->costo=$request->costo;
	$gasto->motivo=$request->motivo;
	return response()->json($gasto, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gasto $gasto)
    {
        //
	$gasto->delete();
	return response()->json(null, 204);
    }
}
