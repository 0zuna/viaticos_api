<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anticipo extends Model
{
    //
	public function user()
	{
		return $this->belongsTo('App\User');
	}
	public function viaje()
	{
		return $this->belongsTo('App\Viaje');
	}
}
