@extends('layouts.app')
@section('content')
<div class="container">
	<div class="row justify-content-center">
		<div class="row">
		@foreach ($viajes as $viaje)
			<div class="col-sm-12">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">{{$viaje['motivo']}}</h5>
					<h6 class="card-subtitle mb-2 text-muted">{{$viaje['status']}}</h6>
					<p class="card-text">Inicio: {{$viaje['inicio']}} Fin:{{$viaje['fin']}}</p>
					<a href="#" class="card-link" data-toggle="modal" data-target="#exampleModal">Gastos</a>
					<a href="#" class="card-link">Anticipos</a>
				</div>
			</div>
			</div>
		@endforeach
		</div>
	</div>
</div>
@endsection
