<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       www.danielboggiano.com
 * @since      1.0.0
 *
 * @package    Cuentas
 * @subpackage Cuentas/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cuentas
 * @subpackage Cuentas/public
 * @author     Daniel Boggiano <dev@danielboggiano.com>
 */
class Cuentas_Subcategoria_Models {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
    
	public function usuario_has_empresa($usuario, $categoria){
		global $wpdb;
		$query = "SELECT * FROM cu_users_has_empresas AS u
		INNER JOIN cu_empresas AS e 
			ON  e.id = u.empresa_id
		INNER JOIN cu_categorias as c
			ON u.empresa_id = c.empresa_id
		WHERE c.id = $categoria AND u.user_id = $usuario;";
		$results = $wpdb->get_results($query, OBJECT);
		if(sizeof($results) > 0)
			return true;
		else
			return false;
	}
    
	public function categoria_has_subcategoria($categoria, $subcategoria){
		global $wpdb;
		$query = "SELECT * FROM cu_subcategorias WHERE categoria_id = $categoria AND id = $subcategoria;";
		$results = $wpdb->get_results($query, OBJECT);
		if(sizeof($results) > 0)
			return true;
		else
			return false;
	}
	
	public function get_subcategoria_data($subcategoria_id){
		global $wpdb;

		$query = "SELECT id, nombre FROM cu_subcategorias WHERE id = $subcategoria_id LIMIT 1";
		$results = $wpdb->get_results($query, OBJECT);

		return $results;
	}

    public function list_all($usuario, $categoria, $busqueda_campo = null, $busqueda_valor = null, $orden_campo = null, $orden_valor = null, $filas = null, $pagina = null){
        global $wpdb;
        $busqueda = ($busqueda_campo == null || $busqueda_valor == null) ? "" : "AND $busqueda_campo LIKE '%$busqueda_valor%'";
        $orden = ($orden_campo == null || $orden_valor == null) ? "" : "ORDER BY $orden_campo $orden_valor";
        $limit = ($filas == null) ? "LIMIT 10" : "LIMIT $filas";

        if($usuario == null || !is_numeric($usuario) || !$this->usuario_has_empresa($usuario, $categoria)){
            return array("success" => false, "error" => "No estás autorizado");
        }

        $sql = "SELECT id FROM cu_subcategorias AS c WHERE c.categoria_id = $categoria $busqueda";
        $wpdb->get_results($sql, OBJECT);

        $filas_total = $wpdb->num_rows;
        $paginas_total = round($filas_total / $filas, 0, PHP_ROUND_HALF_UP);
        $offset = "OFFSET ".($pagina * $filas - $filas);

        $sql = "SELECT id, nombre FROM cu_subcategorias AS c WHERE c.categoria_id = $categoria $busqueda $orden $limit $offset;";
        $data = $wpdb->get_results($sql, OBJECT);
        return array(
            "success" => true,
            "data" => $data,
            "pagina" => array(
                "total_filas" => $filas_total,
                "total_paginas" => $paginas_total
            ),
        );

    }

    public function list_one($usuario, $categoria, $subcategoria){
        global $wpdb;
        
        if($usuario == null || !is_numeric($usuario) || !$this->usuario_has_empresa($usuario, $categoria)){
            return array(
				"success" => false, 
				"error" => "No estás autorizado"
			);
        }

        $sql = "SELECT id, nombre FROM cu_subcategorias AS c
		WHERE c.categoria_id = $categoria
		AND c.id = $subcategoria;";
        $data = $wpdb->get_results($sql, OBJECT);

        return array(
            "success" => true,
            "data" => $data,
        );

    }

	public function create($usuario_id, $categoria_id, $nueva_subcategoria){
		global $wpdb;
		
		if($usuario_id == null || !is_numeric($usuario_id) || !$this->usuario_has_empresa($usuario_id, $categoria_id)){
			return array(
				"success" => false, 
				"error" => "No estás autorizado"
			);
        }
		
		$wpdb->insert('cu_subcategorias', $nueva_subcategoria);
		$subcategoria_id = $wpdb->insert_id;
		$data = $this->get_subcategoria_data($subcategoria_id);

		if(sizeof($data) > 0){
			return array("success" => true, "data" => $data);
		}
		else{
			return array("success" => false, "error" => "No se pudo crear la subcategoria");
		}
	}

	public function update($usuario_id, $categoria_id, $subcategoria_id, $data){
		global $wpdb;
		
		if($this->usuario_has_empresa($usuario_id, $categoria_id)){
			$update = $wpdb->update('cu_subcategorias', $data, array("id" => $subcategoria_id));
			if($update == 0 || !$update){
				return array("success" => false, "error" => "No se pudo actualizar");
			}
			else{
				$data = $this->get_subcategoria_data($subcategoria_id);
				return array("success" => true, "data" => $data);
			}
		}
		else{
			return array("success" => false, "error" => "No estás autorizado");
		}
	}

	public function delete($usuario_id, $categoria_id, $subcategoria_id){
		global $wpdb;

		if($this->usuario_has_empresa($usuario_id, $categoria_id) && $this->categoria_has_subcategoria($categoria_id, $subcategoria_id)){
			$deleted = $wpdb->delete("cu_subcategorias", array("id" => $subcategoria_id));
			if($deleted > 0)
				return array("success" => true);
			else
				return array("success" => false, "error" => "No se pudo borrar el registro");
		}
		else{
			return array("success" => false, "error" => "No estás autorizado");
		}
	}

}