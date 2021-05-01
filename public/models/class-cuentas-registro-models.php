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
class Cuentas_Registro_Models {

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
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $_cuenta_models;

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
        $this->load_dependencies();
        $this->_cuenta_models = new Cuentas_Cuenta_Models( $this->plugin_name, $this->version );

	}

    private function load_dependencies(){
        require_once 'class-cuentas-cuenta-models.php';
    }
    
	public function usuario_has_empresa($usuario, $cuenta){
		global $wpdb;
		$query = "SELECT * FROM cu_users_has_empresas AS u
		INNER JOIN cu_empresas AS e 
			ON  e.id = u.empresa_id
		INNER JOIN cu_cuentas as c
			ON e.id = c.empresa_id
		WHERE c.id = $cuenta AND u.user_id = $usuario;";
		$results = $wpdb->get_results($query, OBJECT);
		if(sizeof($results) > 0)
			return true;
		else
			return false;
	}
    
	public function cuenta_has_registro($cuenta, $registro){
		global $wpdb;
		$query = "SELECT * FROM cu_registros WHERE cuenta_id = $cuenta AND id = $registro;";
		$results = $wpdb->get_results($query, OBJECT);
		if(sizeof($results) > 0)
			return true;
		else
			return false;
	}
	
	public function get_registro_data($registro_id){
		global $wpdb;

		$query = "SELECT r.id, r.fecha, r.ingreso, r.descripcion, r.entidad, r.operacion, r.monto, r.imagen, r.cuenta_id, r.categoria_id, c.nombre as categoria, r.subcategoria_id, s.nombre as subcategoria
        FROM cu_registros AS r
        INNER JOIN cu_categorias AS c ON r.categoria_id = c.id
        LEFT JOIN cu_subcategorias AS s ON r.subcategoria_id = s.id
        WHERE r.id = $registro_id LIMIT 1";
		$results = $wpdb->get_results($query, OBJECT);

		return $results;
	}

    public function list_all($usuario, $cuenta, $busqueda_campo = null, $busqueda_valor = null, $orden_campo = null, $orden_valor = null, $filas = null, $pagina = null){
        global $wpdb;
        $busqueda = ($busqueda_campo == null || $busqueda_valor == null) ? "" : "AND $busqueda_campo LIKE '%$busqueda_valor%'";
        $orden = ($orden_campo == null || $orden_valor == null) ? "" : "ORDER BY $orden_campo $orden_valor";
        $limit = ($filas == null) ? "LIMIT 10" : "LIMIT $filas";

        if($usuario == null || !is_numeric($usuario) || !$this->usuario_has_empresa($usuario, $cuenta)){
            return array("success" => false, "error" => "No estás autorizado");
        }

        $sql = "SELECT id FROM cu_registros AS c WHERE c.cuenta_id = $cuenta AND c.deletedAt IS NULL $busqueda";
        $wpdb->get_results($sql, OBJECT);

        $filas_total = $wpdb->num_rows;
        $paginas_total = ceil($filas_total / $filas);
        $offset = "OFFSET ".($pagina * $filas - $filas);

        $sql = "SELECT r.id, r.fecha, r.ingreso, r.descripcion, r.entidad, r.operacion, r.monto, r.imagen, r.cuenta_id, r.categoria_id, c.nombre as categoria, r.subcategoria_id, s.nombre as subcategoria 
        FROM cu_registros AS r
        INNER JOIN cu_categorias AS c ON r.categoria_id = c.id
        LEFT JOIN cu_subcategorias AS s ON r.subcategoria_id = s.id
        WHERE r.cuenta_id = $cuenta AND r.deletedAt IS NULL $busqueda $orden $limit $offset;";
        $data = $wpdb->get_results($sql, OBJECT);
        return array(
            "success" => true,
            "data" => $data,
			// "query" => $sql,
            "pagina" => array(
                "total_filas" => $filas_total,
                "total_paginas" => $paginas_total
            ),
        );

    }

    public function list_one($usuario, $cuenta, $registro){
        global $wpdb;
        
        if($usuario == null || !is_numeric($usuario) || !$this->usuario_has_empresa($usuario, $cuenta)){
            return array(
				"success" => false, 
				"error" => "No estás autorizado"
			);
        }

        $sql = "SELECT r.id, r.fecha, r.ingreso, r.descripcion, r.entidad, r.operacion, r.monto, r.imagen, r.cuenta_id, r.categoria_id, c.nombre as categoria, r.subcategoria_id, s.nombre as subcategoria
        FROM cu_registros AS r
        INNER JOIN cu_categorias AS c ON r.categoria_id = c.id
        LEFT JOIN cu_subcategorias AS s ON r.subcategoria_id = s.id
		WHERE r.cuenta_id = $cuenta
		AND r.deletedAt IS NULL
		AND r.id = $registro;";
        $data = $wpdb->get_results($sql, OBJECT);

        return array(
            "success" => true,
            "data" => $data,
        );

    }

	public function create($usuario_id, $cuenta_id, $nueva_registro){
		global $wpdb;
		
		if($usuario_id == null || !is_numeric($usuario_id) || !$this->usuario_has_empresa($usuario_id, $cuenta_id)){
			return array(
				"success" => false, 
				"error" => "No estás autorizado"
			);
        }
		
		$wpdb->insert('cu_registros', $nueva_registro);
		$registro_id = $wpdb->insert_id;
		$data = $this->get_registro_data($registro_id);

        $this->_cuenta_models->update_saldo($cuenta_id);

		if(sizeof($data) > 0){
			return array("success" => true, "data" => $data);
		}
		else{
			return array("success" => false, "error" => "No se pudo crear la registro");
		}
	}

	public function update($usuario_id, $cuenta_id, $registro_id, $data){
		global $wpdb;
		
		if($this->usuario_has_empresa($usuario_id, $cuenta_id)){
			$update = $wpdb->update('cu_registros', $data, array("id" => $registro_id));
			if($update == 0 || !$update){
                return array("success" => false, "error" => "No se pudo actualizar");
			}
			else{
                $this->_cuenta_models->update_saldo($cuenta_id);
				$data = $this->get_registro_data($registro_id);
				return array("success" => true, "data" => $data);
			}
		}
		else{
			return array("success" => false, "error" => "No estás autorizado");
		}
	}

	public function delete($usuario_id, $cuenta_id, $registro_id){
		global $wpdb;

		if($this->usuario_has_empresa($usuario_id, $cuenta_id) && $this->cuenta_has_registro($cuenta_id, $registro_id)){
			$deleted = $wpdb->update("cu_registros",array("deletedAt" => current_time('timestamp')), array("id" => $registro_id));
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