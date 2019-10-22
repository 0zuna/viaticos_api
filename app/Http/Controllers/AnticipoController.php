<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

use App\Gasto;
use App\Viaje;
use App\Anticipo;

class AnticipoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
	    return response()->json($request->user()->anticipos()->get());
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
		$anticipo = new Anticipo();
		$anticipo->anticipo=$request->NewAnticipo;
		$anticipo->user_id=$request->user()->id;
		$anticipo->viaje_id=$request->id;
		$anticipo->save();
		$id=$anticipo->id;
	    	
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
		//return response()->json($anticipo, 201);
		$path = storage_path().'/img/'.$request->user()->id.'/viajes/anticipos/'.$request->viaje_id;
		if(!\File::exists($path)) {
			\File::makeDirectory($path, $mode = 0777, true, true);
		}
		file_put_contents($path.'/'.$id.'.jpg', base64_decode($request->imagen));
		return response()->json($viajes, 201);
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
    public function update(Request $request, Anticipo $anticipo)
    {
        //
	$anticipo->anticipo=$request->anticipo;
	$anticipo->update();
	return response()->json($anticipo, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Anticipo $anticipo)
    {
        //
	$anticipo->delete();
	return response()->json(null, 204);
    }
}
