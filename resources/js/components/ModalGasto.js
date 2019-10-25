import React, { useState } from 'react'

const ModalGasto = ({gastos, setGastos, newGasto, setNewGasto, viajes, setViajes, model}) => {


	const _newGasto=()=>{
		const viaje_id=model.viaje_id
		const user_id=model.user_id
		var reader = new FileReader();
		reader.readAsDataURL(newGasto.imagen);
		reader.onload = () => {
			axios.post('/gasto',{...newGasto,imagen:reader.result})
			.then(r=>{
				setGastos([...gastos,r.data])
				setNewGasto({...newGasto,form:false})

				const viajs=viajes.map(v=>{
					if(v.id==user_id){
						const viajess=v.viajes.map(vi=>{
							if(vi.id==viaje_id){
								return {...vi,gastos:[...vi.gastos,r.data]}
							}
							return vi
						})
						return {...v,viajes:viajess}
					}
					return v
				})
				setViajes(viajs)

			})
		}
	}

	const _destroyGasto=(g)=>{
		axios.delete('deletegasto',{params:g})
		.then(r=>{
			const gasti=gastos.filter((a)=>a.id!==g.id)
			setGastos(gasti)
		})
	}

	return (
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
	)
}

export default ModalGasto
