
'use strict';

/**
 * All of the code for your admin-facing JavaScript source
 * should reside in this file.
 *
 * Note: It has been assumed you will write jQuery code here, so the
 * jQuery function reference has been prepared for usage within the scope
 * of this function.
 *
 * This enables you to define handlers, for when the DOM is ready:
 *
 * jQuery(function() {
 *
 * });
 *
 * When the window is loaded:
 *
 * jQuery( window ).load(function() {
 *
 * });
 *
 * ...and/or other possibilities.
 *
 * Ideally, it is not considered best practise to attach more than a
 * single DOM-ready or window-load handler for a particular page.
 * Although scripts in the WordPress core, Plugins and Themes may be
 * practising this, we should strive to set a better example in our own work.
 */

const fillDataTable = ({data, pageId, columns, tableId}) => {
	return new Promise((resolve, reject) => {
		jQuery(pageId).find("#filters").html(`
			<form class="form-row" id="formFilters">
				<div class="col">
					<input type="search" name="buscar" placeholder="Filtrar por..." class="form-control" id="searchField">
				</div>
				<div class="col">
					<select id="selectCampos" name="campo">
					</select>
				</div>
				<div class="col">
					<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
				</div>
			</form>
		`)
		const filters = jQuery(pageId).find("#formFilters")
		const search = jQuery(pageId).find("#searchField")
		const select = jQuery(pageId).find("#selectCampos")
		select.html('')
		columns.map( c => {
			if (c.visible && c.key != 'actions' && c.key != 'imagen')
				select.append(`<option value="${c.key}">${c.title}</option>`)
		})
		
		jQuery(pageId).find(tableId).html(``)
		jQuery(pageId).find(tableId).append(`<thead>`)
		jQuery(pageId).find(tableId).append(`<tr>`)
		columns.map( c => {
			if(c.visible)
				jQuery(pageId).find(tableId).append(`<th>${c.title.toUpperCase()}</th>`)			
		})
		jQuery(pageId).find(tableId).append(`</tr>`)
		jQuery(pageId).find(tableId).append(`</thead>`)
		
		data.map( d => {
			jQuery(pageId).find(tableId).append(`<tr>`)
			columns.map( c => {
				if(c.visible){
					if(c.key == 'imagen'){
						jQuery(pageId).find(tableId).append(`<td><img class="img-thumbnail" src="${'http://picsum.photos/50/50'}"/></td>`)
					}
					else if(c.key == 'actions'){
						jQuery(pageId).find(tableId).append(`
							<td>
								<button class="btn" id="edit_${d.id}"><i class="fas fa-edit mr-1"></i></button>
								<button class="btn" id="trash_${d.id}"><i class="fas fa-trash mb-1"></i></button>
								<button class="btn" id="watch_${d.id}"><i class="fas fa-eye mr-1"></i></button>
							</td>
						`)
						jQuery(pageId).find(`#edit_${d.id}`).attr('value', d.id)
						jQuery(pageId).find(`#trash_${d.id}`).attr('value', d.id)
						jQuery(pageId).find(`#watch_${d.id}`).attr('value', d.id)
					}
					else{
						jQuery(pageId).find(tableId).append(`<td>${d[c.key]}</td>`)
					}
				}
			})
			jQuery(pageId).find(tableId).append(`</tr>`)
		})
		const watchers = jQuery(pageId).find("[id*='watch_']")
		const editors = jQuery(pageId).find("[id*=edit_")
		const deleters = jQuery(pageId).find("[id*=trash_")
		jQuery(pageId).find(tableId).append(`</tbody>`)
		jQuery(pageId).find(tableId).append(`<tfoot>`)
		jQuery(pageId).find(tableId).append(`<tr>`)
		columns.map( c => {
			if(c.visible)
				jQuery(pageId).find(tableId).append(`<th>${c.title.toUpperCase()}</th>`)			
		})
		jQuery(pageId).find(tableId).append(`</tr>`)
		jQuery(pageId).find(tableId).append(`</tfoot>`)

		resolve({search, select, filters, deleters, editors, watchers})
	})
}

const pagination = ({total_filas, total_paginas, length, pageId, paginationId }) => {
	return new Promise((resolve, reject) => {
		jQuery(pageId).find(paginationId).html(``)
		jQuery(pageId).find(paginationId).append(`<p>Mostrando ${length} de ${total_filas}</p>`)
		jQuery(pageId).find(paginationId).append(`
				<li class="page-item" id="page_p">
				<a class="page-link" href="#" aria-label="Previous">
					<span aria-hidden="true">&laquo;</span>
					<span class="sr-only">Previous</span>
				</a>
				</li>
			`)
		jQuery(`#page_p`).attr('value', '-1')
		for(let i=1; i<= total_paginas; i++){
			jQuery(pageId).find(paginationId).append(`<li class="page-item" id="page_${i}"><a class="page-link" href="#">${i}</a></li>`)
			jQuery(`#page_${i}`).attr('value', i)
		}
		jQuery(pageId).find(paginationId).append(`
			<li class="page-item" id="page_n">
			<a class="page-link" href="#" aria-label="Next">
				<span aria-hidden="true">&raquo;</span>
				<span class="sr-only">Next</span>
			</a>
			</li>
		`)
		jQuery(`#page_n`).attr('value', 0)
		const page = jQuery("[id*='page_']")
		resolve(page)
	})
}
