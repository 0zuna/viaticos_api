<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Viaje;

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
	    return response()->json($request->user()->viajes()->get());
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
		$viaje->anticipo=$request->anticipo;
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
}
