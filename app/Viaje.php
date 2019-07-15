<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
	//
	public function user()
	{
		return $this->belongsTo('App\User');
	}
	public function gastos()
	{
		return $this->hasMany('App\Gasto');
	}
	public function anticipos()
	{
		return $this->hasMany('App\Anticipo');
	}
}
