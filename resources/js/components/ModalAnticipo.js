import React, { useState } from 'react'

const ModalAnticipo = ({anticipos, setAnticipos, newAnticipo, setNewAnticipo, viajes, setViajes, model}) => {


	const _newAnticipo=()=>{
		const viaje_id=model.viaje_id
		const user_id=model.user_id
		axios.post('/anticipo',newAnticipo)
		.then(r=>{
			setAnticipos([...anticipos,r.data])
			setNewAnticipo({...newAnticipo,form:false})

				const viajs=viajes.map(v=>{
					if(v.id==user_id){
						const viajess=v.viajes.map(vi=>{
							if(vi.id==viaje_id){
								return {...vi,anticipos:[...vi.anticipos,r.data]}
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

	const _destroyAnticipo=(an)=>{
		axios.delete('deleteanticipo',{params:an})
		.then(r=>{
			const anti=anticipos.filter((a)=>a.id!==an.id)
			setAnticipos(anti)
		})
	}

	return (
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
	)
}

export default ModalAnticipo
