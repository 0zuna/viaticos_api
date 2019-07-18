<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Viaje;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
	    $users=User::orderBy('created_at','desc')
		    ->with(['viajes'=>function($q){
				$q->with(['gastos','anticipos']);
			}]
		    )
		    ->get()->toarray();
	    
	   /* foreach ($viajes as $k=>$v) {
	    	$gasto_total=array_reduce($v['gastos'],function($v,$w){
			return $v+$w['costo'];
		});
	    	$anticipo=array_reduce($v['anticipos'],function($v,$w){
			return $v+$w['anticipo'];
		});
		$viajes[$k]['anticipo']=number_format($anticipo,2);
		$viajes[$k]['disponible']=number_format($anticipo-$gasto_total,2);
	    }*/
        return view('home',compact('users'));
    }
}
