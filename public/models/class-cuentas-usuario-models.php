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
class Cuentas_Usuario_Models {

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

    // MODELS
    public function list_all($usuario, $busqueda_campo = null, $busqueda_valor = null, $orden_campo = null, $orden_valor = null, $filas = null, $pagina = null){
        global $wpdb;
        $busqueda = ($busqueda_campo == null || $busqueda_valor == null) ? "" : "AND $busqueda_campo LIKE '%$busqueda_valor%'";
        $orden = ($orden_campo == null || $orden_valor == null) ? "" : "ORDER BY $orden_campo $orden_valor";
        $limit = ($filas == null) ? "LIMIT 10" : "LIMIT $filas";

        if($usuario == null || !is_numeric($usuario)){
            return array("success" => false, "error" => "No estás autorizado");
        }

        $sql = "SELECT id FROM cu_usuarios AS e INNER JOIN cu_users_has_usuarios AS u ON e.id = u.usuario_id WHERE u.user_id = $usuario $busqueda";
        $wpdb->get_results($sql, OBJECT);
        $filas_total = $wpdb->num_rows;
        $paginas_total = round($filas_total / $filas, 0, PHP_ROUND_HALF_UP);
        $offset = "OFFSET ".($pagina * $filas - $filas);

        $sql = "SELECT id, nombre, notas, image_url FROM cu_usuarios AS e INNER JOIN cu_users_has_usuarios AS u ON e.id = u.usuario_id WHERE u.user_id = $usuario $busqueda $orden $limit $offset;";
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

    public function list_one($usuario){
        global $wpdb;
        
        if($usuario == null || !is_numeric($usuario)){
            return array(
				"success" => false, 
				"error" => "No estás autorizado"
			);
        }

        $sql = "SELECT id, nombre, notas, image_url FROM cu_usuarios AS e 
		INNER JOIN cu_users_has_usuarios AS u 
		ON e.id = u.usuario_id 
		WHERE u.user_id = $usuario 
		AND e.id = $usuario;";
        $data = $wpdb->get_results($sql, OBJECT);
        return array(
            "success" => true,
            "data" => $data,
        );

    }
    
	public function usuario_has_usuario($usuario){
		global $wpdb;
		$query = "SELECT * FROM cu_users_has_usuarios AS u WHERE u.usuario_id = $usuario AND u.user_id = $usuario;";
		$results = $wpdb->get_results($query, OBJECT);
		if(sizeof($results) > 0)
			return true;
		else
			return false;
	}
	
	public function get_usuario_data($usuario_id){
		global $wpdb;

		$query = "SELECT e.id, e.nombre, e.notas, e.image_url FROM cu_usuarios AS e INNER JOIN cu_users_has_usuarios AS u ON e.id = u.usuario_id WHERE e.id = $usuario_id LIMIT 1";
		
		$results = $wpdb->get_results($query, OBJECT);
		return $results[0];
	}

	public function create($usuario_id, $nueva_usuario){
		global $wpdb;

		if($usuario_id == null || !is_numeric($usuario_id)){
            return array(
				"success" => false, 
				"error" => "No estás autorizado"
			);
        }

		$wpdb->insert('cu_usuarios', $nueva_usuario);
		$usuario_id = $wpdb->insert_id;

		if( $usuario_id > 0){
			$wpdb->insert('cu_users_has_usuarios', array(
				"usuario_id" => $usuario_id,
				"user_id" => $usuario_id
			));
		}
		else{
			return array("success" => false, "error" => "No se pudo crear la usuario");
		}

		$data = $this->get_usuario_data($usuario_id, $usuario_id);
		return array("success" => true, "data" => array($data));
	}

	public function update($usuario_id, $data){
		global $wpdb;

		if($this->usuario_has_usuario($usuario_id, $usuario_id)){
			$update = $wpdb->update('cu_usuarios', $data, array("id" => $usuario_id));
			if($update == 0 || !$update){
				return array("success" => false, "error" => "No se pudo actualizar");
			}
			else{
				$data = $this->get_usuario_data($usuario_id, $usuario_id);
				return array("success" => true, "data" => array($data));
			}
		}
		else{
			return array("success" => false, "error" => "No estás autorizado");
		}

	}

	public function add_user($usuario_id, $data){
		global $wpdb;
		if($this->usuario_has_usuario($usuario_id, $usuario_id)){
			$insert = $wpdb->insert('cu_users_has_usuarios', $data);
			if($insert){
				return array("success" => true);
			}
			else{
				return array("success" => false, "error" =>$wpdb->last_error);
			}
		}
		else{
			return array("success" => false, "error" => "No estás autorizado");
		}
		
	}

	public function delete($usuario_id){
		global $wpdb;

		if($this->usuario_has_usuario($usuario_id, $usuario_id)){
			$wpdb->delete('cu_users_has_usuarios', array("user_id" => $usuario_id, "usuario_id" => $usuario_id));
			$wpdb->delete("cu_usuarios", array("id" => $usuario_id));
			return array("success" => true);
		}
		else{
			return array("success" => false, "error" => "No estás autorizado");
		}
	}

}