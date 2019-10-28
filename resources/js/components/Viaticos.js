import React, { Component, useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import DayPicker from 'react-day-picker';
import DayPickerInput from 'react-day-picker/DayPickerInput';
import 'react-day-picker/lib/style.css';
import ModalGasto from './ModalGasto'
import ModalAnticipo from './ModalAnticipo'

const MONTHS = ['Enero','Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
const WEEKDAYS_LONG = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
const WEEKDAYS_SHORT = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'];

const Viajes = () => {
	const [data, setData]=useState({})
	const [users, setUsers]=useState([])
	const [gastos, setGastos]=useState([])
	const [viajes, setViajes]=useState([{viajes:[]}])
	const [anticipos, setAnticipos]=useState([])
	const [newGasto, setNewGasto]=useState({form:false})
	const [newAnticipo, setNewAnticipo]=useState({form:false})
	const [model, setModel]=useState({user_id:'',viaje_id:''})
	const [adeudos, setAdeudos]=useState([])

	useEffect(()=>{
		$('#js-example-basic-multiple').select2();
	},[])

	const _getUsers=(d)=>{
		axios.get('/users',{params: {departamento:d}})
		.then((r)=>{
			setUsers(r.data)
		})
	}

	const _buscar=()=>{
		setAdeudos([])
		var users=$('#js-example-basic-multiple').select2('val')
		setData({...data,users:users})
		axios.get('/viaje',{params: {...data,users:users}})
		.then((r)=>{
			setViajes(r.data)
		}).catch(r=>setViajes([]))
	}

	const _excel=()=>{
		var users=$('#js-example-basic-multiple').select2('val')
		setData({...data,users:users})
		const uri=(axios.defaults.baseURL+'excel?'+$.param({...data,users:users}))
		window.open(uri)
		axios.get('/excel',{params: {...data,users:users}})
		.then((r)=>{
			console.log(r.data)
		})
	}

	const _destroyViaje=(v)=>{
		axios.delete('deleteviaje',{params:v})
		.then(r=>{
			const via=viajes.map(u=>{
				u.viajes=u.viajes.filter((a)=>a.id!==v.id)
				return u
			})
			setViajes(via)
		})
	}

	const _excelViaje = (viaje) => {
		const uri=(axios.defaults.baseURL+'excel_viaje?'+$.param({viaje_id:viaje}))
		window.open(uri)
	}

	const _adeudosTotales = () => {
		setViajes([])
		var users=$('#js-example-basic-multiple').select2('val')
		axios.post('/adeudos',{users})
		.then(r=>{
			console.log(r.data)
			setAdeudos(r.data)
		})
		.catch(r=>console.log(r))
	}

	return (
		<div className="container">
			<div className='float-right'>
				<button onClick={_excel} type="button" className="btn btn-success">Excel Resumen</button>
			</div>
			<ModalAnticipo anticipos={anticipos} setAnticipos={setAnticipos} newAnticipo={newAnticipo} setNewAnticipo={setNewAnticipo} setViajes={setViajes} viajes={viajes} model={model}/>
			<ModalGasto gastos={gastos} setGastos={setGastos} newGasto={newGasto} setNewGasto={setNewGasto} setViajes={setViajes} viajes={viajes} model={model}/>
			<div className="row justify-content-center">
				<div>
				<p>inicio</p>
					<DayPickerInput 
						inputProps={{ style: { width: 100 } }}
						dayPickerProps={{months: MONTHS,weekdaysLong: WEEKDAYS_LONG,weekdaysShort:WEEKDAYS_SHORT,firstDayOfWeek: 1}}
						onDayChange={day => setData({...data,inicio:day.toISOString().split('T')[0]})} />
				</div>
				<div>
				<p>fin</p>
					<DayPickerInput
						inputProps={{ style: { width: 100 } }}
						dayPickerProps={{months: MONTHS,weekdaysLong: WEEKDAYS_LONG,weekdaysShort:WEEKDAYS_SHORT,firstDayOfWeek: 1}}
						onDayChange={day => setData({...data,fin:day.toISOString().split('T')[0]})} />
				</div>
				<div>
				<p>Departamento</p>
				<select onChange={(e)=>{_getUsers(e.target.value)}} className="form-control">
					<option value=''>selecciona</option>
					<option value='ADMINISTRACIÓN'>ADMINISTRACIÓN</option>
					<option value='OPERACIÓN'>OPERACIÓN</option>
					<option value='COMERCIAL'>COMERCIAL</option>
					<option value='FINANZAS'>FINANZAS</option>
					<option value='OBRAS Y PROYECTOS'>OBRAS Y PROYECTOS</option>
					<option value='DIRECCION DE TRAFICO'>DIRECCION DE TRAFICO</option>
					<option value='all'>TODOS</option>
				</select>
				</div>
				<div>
					<p>User</p>
					<select onChange={()=>setData({...data,users:$('#js-example-basic-multiple').select2('val')})} style={{width:200}} id="js-example-basic-multiple" name="states[]" multiple="multiple">
						<option value='todos'>Todos</option>
						{users.map((user,i)=><option key={i} value={user.id}>{user.colaborador}</option>)}
					</select>
					<button onClick={_buscar} type="button" className="btn btn-dark">Buscar Viajes</button>
					<button onClick={_adeudosTotales} type="button" className="btn btn-dark">Adeudos Totales</button>
				</div>
				<div className="col-md-12">
					<div className="card">
						{viajes.map((u)=>u.viajes.map((v,i)=>
							<div key={i} className="card">
								<div className="card-body">
								<div className="row">
								<div className="col-md-6">
									<a onClick={()=>_destroyViaje(v)} href='#'>eliminar</a>
									<h5 className="card-title">{u.colaborador}</h5>
									<h6 className="card-subtitle mb-2 text-muted">{u.departamento}</h6>
									<h6 className="card-subtitle mb-2 text-muted">{v.motivo}</h6>
									<h6 className="card-subtitle mb-2 text-muted">{v.inicio} a {v.fin}</h6>
									<p className="card-text">telefono: {u.telefono}</p>
									<a href="#" onClick={()=>{setGastos(v.gastos);setNewGasto({...newGasto,viaje_id:v.id,user_id:u.id});setModel({user_id:u.id,viaje_id:v.id})}} className="card-link" data-toggle="modal" data-target=".bd-gastos-modal-xl">Gastos</a>
									<a href="#" onClick={()=>{setAnticipos(v.anticipos);setNewAnticipo({...newAnticipo,viaje_id:v.id,user_id:u.id});setModel({user_id:u.id,viaje_id:v.id})}}className="card-link" data-toggle="modal" data-target=".bd-anticipos-modal-xl">Anticipos</a>
								</div>
								<div className="col">
								<div className="card" style={{top: 30}}>
									<div className="card-header">Adeudo</div>
									<div className="card-body">
										<h4 className="card-title" style={(v.anticipos.reduce((a,b)=>a+parseFloat(b.anticipo),0)-v.gastos.reduce((a,b)=>a+parseFloat(b.costo),0)).toFixed(2)>=0?{color:'green'}:{color:'red'}}>${(v.anticipos.reduce((a,b)=>a+parseFloat(b.anticipo),0)-v.gastos.reduce((a,b)=>a+parseFloat(b.costo),0)).toFixed(2)}</h4>
									</div>
							</div>
								</div>

								<div className="col">
									<button onClick={()=>_excelViaje(v.id)} type="button" className="btn btn-dark float-right">Exportar Viaje</button>
								</div>
								</div>
								</div>
							</div>
							)
						)}
					</div>
						{adeudos.map((a,i)=>
							<div key={i} className="card" style={{marginBottom: 100}}>
							<center>
								<img src={axios.defaults.baseURL+'/icon.png'} className="card-img-top" style={{width:200}}/>
							</center>
								<div className="card-body">
									<h5 className="card-title">{a.colaborador}</h5>
									<p className="card-text">{a.departamento}</p>
									<p className="card-text">{a.telefono}</p>
									<a href={`mailto:${a.email}`} className="card-text">{a.email}</a>
								</div>
								<h4 className="card-title">Viajes</h4>
								<table className="table table-hover">
									<thead>
										<tr>
											<th scope="col">#</th>
											<th scope="col">Motivo</th>
											<th scope="col">Fecha</th>
											<th scope="col">Anticipo Total</th>
											<th scope="col">Gasto Total</th>
											<th scope="col">Adeudo</th>
										</tr>
									</thead>
									<tbody>
										{a.viajes.map((v,i)=>
										<tr key={i}>
											<th key={i}>{i++}</th>
											<td>{v.motivo}</td>
											<td>{v.inicio}</td>
											<td>${(v.anticipoTotal).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
											<td>${(v.gastoTotal).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
											<td>${(v.adeudo).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</td>
										</tr>
										)}
									</tbody>
								</table>
								<div className="col-12">
								<div className="float-right">
									<h4 style={a.adeudoTotal>=0?{color:'green'}:{color:'red'}}>Adeudo total: ${a.adeudoTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,')}</h4>
								</div>
								</div>
							</div>
						)}
				</div>
			</div>
		</div>
	);
}

if (document.getElementById('app')) {
    ReactDOM.render(<Viajes />, document.getElementById('app'));
}
