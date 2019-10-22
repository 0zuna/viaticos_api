import React, { Component, useState, useEffect } from 'react';
import ReactDOM from 'react-dom';
import DayPicker from 'react-day-picker';
import DayPickerInput from 'react-day-picker/DayPickerInput';
import 'react-day-picker/lib/style.css';

const MONTHS = ['Enero','Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
const WEEKDAYS_LONG = [ 'Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];
const WEEKDAYS_SHORT = ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sa'];

const Viajes=()=> {
	const [data, setData]=useState({})
	const [users, setUsers]=useState([])
	const [viajes, setViajes]=useState([{viajes:[]}])
	const [gastos, setGastos]=useState([])
	const [anticipos, setAnticipos]=useState([])
	const [newGasto, setNewGasto]=useState({form:false})
	const [newAnticipo, setNewAnticipo]=useState({form:false})
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
	const _newGasto=()=>{
		var reader = new FileReader();
		reader.readAsDataURL(newGasto.imagen);
		reader.onload = () => {
			axios.post('/gasto',{...newGasto,imagen:reader.result})
			.then(r=>{
				setGastos([...gastos,r.data])
				setNewGasto({...newGasto,form:false})
			})
		}
	}
	const _newAnticipo=()=>{
		console.log(newAnticipo)
		axios.post('/anticipo',newAnticipo)
		.then(r=>{
			setAnticipos([...anticipos,r.data])
			setNewAnticipo({...newAnticipo,form:false})
		})
	}
	const _destroyGasto=(g)=>{
		axios.delete('deletegasto',{params:g})
		.then(r=>{
			const gasti=gastos.filter((a)=>a.id!==g.id)
			setGastos(gasti)
		})
	}
	const _destroyAnticipo=(an)=>{
		axios.delete('deleteanticipo',{params:an})
		.then(r=>{
			const anti=anticipos.filter((a)=>a.id!==an.id)
			setAnticipos(anti)
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
	return (
		<div className="container">
			<div className='float-right'>
			<button onClick={_excel} type="button" className="btn btn-success">Excel export</button>
			</div>
			<div className="modal fade bd-anticipos-modal-xl" tabIndex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
			<div className="modal-dialog modal-xl">
				<div className="modal-content">
					<div className="modal-header">
						<h5 className="modal-title" id="exampleModalLongTitle">Anticipos</h5>
						<button type="button" className="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div className="modal-body">
					<table className="table table-hover">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Anticipo</th>
								<th scope="col">Fecha</th>
								<th scope="col">Foto</th>
								<th scope="col">eliminar</th>
							</tr>
						</thead>
						<tbody>
						{anticipos.map((a,i)=>
							<tr key={i}>
								<th scope="row">{i+1}</th>
								<td>${a.anticipo}</td>
								<td>{a.created_at}</td>
								<td>
								<img style={{height:100}} src={axios.defaults.baseURL+'img/'+a.user_id+'/viajes/'+a.viaje_id+'/anticipos/'+a.id+'.jpg'} alt="imagen" className="img-thumbnail"/>
								</td>
								<td><a href='#' onClick={()=>_destroyAnticipo(a)} >eliminar</a></td>
							</tr>
							)
						}
						</tbody>
					</table>
					{newAnticipo.form&&
						<div className='card'>
						<div className='card-body'>
							Nuevo Anticipo
							<div className="form-group">
								<label>Anticipo</label>
								<input value={newAnticipo.anticipo||''} onChange={(e)=>setNewAnticipo({...newAnticipo,anticipo:e.target.value})} type="number" className="form-control" placeholder="Anticipo"/>
  							</div>
							<button onClick={_newAnticipo} type="submit" className="btn btn-primary">Agregar</button>
						</div>
						</div>
					}
					</div>
					<div className="modal-footer">
						<button onClick={()=>setNewAnticipo({...newAnticipo,form:true})} type="button" className="btn btn-secondary">Nuevo Anticipo</button>
						<button type="button" className="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
			</div>
			<div className="modal fade bd-gastos-modal-xl" tabIndex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
			<div className="modal-dialog modal-xl">
				<div className="modal-content">
					<div className="modal-header">
						<h5 className="modal-title" id="exampleModalLongTitle">Gastos</h5>
						<button type="button" className="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div className="modal-body">
					<table className="table table-hover">
						<thead>
							<tr>
								<th scope="col">#</th>
								<th scope="col">Motivo</th>
								<th scope="col">Costo</th>
								<th scope="col">Fecha</th>
								<th scope="col">Foto</th>
								<th scope="col">eliminar</th>
							</tr>
						</thead>
						<tbody>
						{gastos.map((g,i)=>
							<tr key={i}>
								<th scope="row">{i+1}</th>
								<td>{g.motivo}</td>
								<td>${g.costo}</td>
								<td>{g.created_at}</td>
								<td>
								<img style={{height:100}} src={axios.defaults.baseURL+'img/'+g.user_id+'/viajes/'+g.viaje_id+'/gastos/'+g.id+'.jpg'} alt="imagen" className="img-thumbnail"/>
								</td>
								<td><a href='#' onClick={()=>_destroyGasto(g)} >eliminar</a></td>
							</tr>
							)
						}
						</tbody>
					</table>
					{newGasto.form&&
						<div className='card'>
						<div className='card-body'>
							Nuevo gasto
							<div className="form-group">
								<label>Motivo</label>
								<select defaultValue={newGasto.motivo||'0'} onChange={(e)=>setNewGasto({...newGasto,motivo:e.target.value})} className="custom-select">
									<option value='0'>Seleccionar</option>
									<option value="Transporte">Transporte</option>
									<option value="Hospedaje">Hospedaje</option>
									<option value="Comida">Comida</option>
									<option value="Otros">Otros</option>
								</select>
  							</div>
							<div className="form-group">
								<label>Costo</label>
								<input value={newGasto.costo||''} onChange={(e)=>setNewGasto({...newGasto,costo:e.target.value})} type="number" className="form-control" placeholder="Costo"/>
  							</div>
							<div className="form-group">
								<label>Foto</label>
								<input onChange={(e)=>setNewGasto({...newGasto,imagen:e.target.files[0]})} type="file" className="form-control-file"/>
							</div>
							<button onClick={_newGasto} type="submit" className="btn btn-primary">Agregar</button>
						</div>
						</div>
					}
					</div>
					<div className="modal-footer">
						<button onClick={()=>setNewGasto({...newGasto,form:true})} type="button" className="btn btn-secondary">Nuevo Gasto</button>
						<button type="button" className="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
			</div>
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
				<button onClick={_buscar} type="button" className="btn btn-dark">Buscar</button>
			</div>
			<div className="col-md-12">
				<div className="card">
					{viajes.map((u)=>u.viajes.map((v,i)=>
						<div key={i} className="card">
							<div className="card-body">
								<div className='float-right'><a onClick={()=>_destroyViaje(v)} href='#'>eliminar</a></div>
								<h5 className="card-title">{u.colaborador}</h5>
								<h6 className="card-subtitle mb-2 text-muted">{u.departamento}</h6>
								<h6 className="card-subtitle mb-2 text-muted">{v.motivo}</h6>
								<h6 className="card-subtitle mb-2 text-muted">{v.inicio} a {v.fin}</h6>
								<p className="card-text">telefono: {u.telefono}</p>
								<a href="#" onClick={()=>{setGastos(v.gastos);setNewGasto({...newGasto,viaje_id:v.id,user_id:u.id})}} className="card-link" data-toggle="modal" data-target=".bd-gastos-modal-xl">Gastos</a>
								<a href="#" onClick={()=>{setAnticipos(v.anticipos);setNewAnticipo({...newAnticipo,viaje_id:v.id,user_id:u.id})}}className="card-link" data-toggle="modal" data-target=".bd-anticipos-modal-xl">Anticipos</a>
							</div>
						</div>
						)
					)}
				</div>
			</div>
			</div>
		</div>
	);
}

if (document.getElementById('app')) {
    ReactDOM.render(<Viajes />, document.getElementById('app'));
}
