<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.danielboggiano.com
 * @since      1.0.0
 *
 * @package    Cuentas
 * @subpackage Cuentas/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div id="empresas">
    <div id="layoutSidenav_content">
                <!-- <main> -->
                    <!-- <div class="container-fluid"> -->
                        <h1 class="mt-4" id="title"></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item"><a href="/wp-admin/admin.php?page=mm-index">Inicio</a></li>
                            <li class="breadcrumb-item active">Empresas</li>
                        </ol>
                        <div class="" id="content">
                            <div class="card-header" id="filters"></div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0"></table>

                                </div>
                            </div>
                            <div class="card-footer">                                
                                <nav aria-label="Page navigation">
                                    <ul class="pagination" id="paginacion"></ul>
                                </nav>
                            </div>
                        </div>
                    <!-- </div> -->
                <!-- </main> -->
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid">
                        
                    </div>
                </footer>
            </div>
</div>

<script>
(function( $ ) {
	'use strict';
	$(document).ready(async () => {
		$('#empresas').find('#title').html('Empresas');

        const options = {
            url: "/wp-json/cuentas/v1/empresa",
            method: "GET",
            timeout: 0,            
            query: {
                pagina: 1,
                filas: 10,
                busqueda_campo: '',
                busqueda_valor: '',
                orden_campo: 'id',
                order_valor: 'asc',
            },
            params: {},
            headers: { "token": ''},
            body: {},
        }

        const table = {
            pageId: '#empresas',
            tableId: '#dataTable',
            actions: true,
            columns: [
                {key: 'id', title: 'ID', visible: true},
                {key: 'imagen', title: 'IMAGEN', visible: true},
                {key: 'nombre', title: 'NOMBRE', visible: true},
                {key: 'notas', title: 'NOTAS', visible: true},
                {key: 'actions', title: 'ACCIONES', visible: true},
            ]
        }
        
        const fetchData = ({url, method, timeout, params, query, headers}) => {
                url += '/'
                Object.keys(params).map( key => {
                    if(params[key])
                        url += `${params[key]}/`
                })
                Object.keys(query).map( (key, index) => {
                    if(index === 0){
                        url += `?${key}=${query[key]}`
                    }
                    else{
                        if(query[key])
                            url += `&${key}=${query[key]}`
                    }
                })
                $.ajax({url: url, type: method, timeout}).done(response => {
                    fillDataTable({
                        data: response.data,
                        ...table
                    }).then( (res) => {
                        res.filters.submit( e => {
                            e.preventDefault()
                            changeFilters(res.select.val(), res.search.val())
                        })
                        res.deleters.click( e => deleteItem(e.currentTarget.value))
                        res.editors.click( e => editItem(e.currentTarget.value))
                        res.watchers.click( e => seeItem(e.currentTarget.value) )
                    })

                    pagination({
                        ...response.pagina,
                        length: response.data.length,
                        pageId: "#empresas",
                        paginationId: "#paginacion"
                    }).then( page => {
                        page.click( e => changePage(e.currentTarget.value, response.pagina.total_paginas) )
                    })
                })
        }
        const fetchDelete = ({url, timeout, params, headers}) => {
                url += '/'
                Object.keys(params).map( key => {
                    if(params[key])
                        url += `${params[key]}/`
                })
                $.ajax({url: url, type: 'DELETE', timeout}).done(response => {
                    if(response.success)
                        fetchData(options)
                    else
                        alert(response.message)
                })
        }

        await fetchData(options)

        const changePage = (page, total) => {
            if(page == '-1' && options.query.pagina > 1){
                options.query.pagina -= 1
                fetchData(options)
            }
            if(page == 0 && options.query.pagina < total){
                options.query.pagina += 1
                fetchData(options)
            }
            if(page != options.query.pagina && page > 0 && page <= total){
                options.query.pagina = page
                fetchData(options)
            }
        }

        const changeFilters = (campo, valor) => {
            let query = {
                pagina: options.query.pagina,
                filas: options.query.filas,
                busqueda_valor: valor,
                busqueda_campo: campo,
                orden_campo: options.query.orden_campo,
                orden_valor: options.query.orden_valor
            }
            let params = {}
            fetchData({
                url: options.url,
                method: options.method,
                timeout: options.timeout,
                headers: options.headers,
                params,
                query
            })
        }

        const deleteItem = id => {
            if(confirm('Estás seguro que deseas borrar este elemento')){
                fetchDelete({
                    url: options.url, 
                    params: {id}, 
                    timeout: options.timeout, 
                    headers: options.headers
                })
            }
        }

        const editItem = id => {
            console.log(id)
        }

        const seeItem = id => {
            console.log(id)
        }
        
	})

})( jQuery );
</script>