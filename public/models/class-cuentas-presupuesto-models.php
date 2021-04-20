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
class Cuentas_Presupuesto_Models {

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

	private function process_results($data){
		$presupuestos = array();
		$categorias = array();
		$subcategorias = array();
		$ids = array();
		$results = array();

		for($i = 0; $i < sizeof($data); $i++){
			$pres = array(
				"id" => $data[$i]->id,
				"nombre" => $data[$i]->nombre,
				"inicio" => $data[$i]->inicio,
				"fin" => $data[$i]->fin,
				"ingreso" => $data[$i]->ingreso,
				"moneda" => $data[$i]->moneda,
				"empresa_id" => $data[$i]->empresa_id,
				"cuenta_id" => $data[$i]->cuenta_id,
				"cuenta" => $data[$i]->cuenta,
			);
			$cat = array(
				"cat_id" => $data[$i]->cat_id,
				"cat_nombre" => $data[$i]->cat_nombre,
				"cat_meta" => $data[$i]->cat_meta,
				"cat_gasto" => $data[$i]->cat_gasto,
				"cat_diferencia" => $data[$i]->cat_diferencia,
			);
			$sub = array(
				"sub_id" => $data[$i]->sub_id,
				"sub_cat" => $data[$i]->sub_cat,
				"sub_nombre" => $data[$i]->sub_nombre,
				"sub_meta" => $data[$i]->sub_meta,
				"sub_gasto" => $data[$i]->sub_gasto,
				"sub_diferencia" => $data[$i]->sub_diferencia,
			);
			
			$id = $pres['id'];
			
			if(!isset($presupuestos[$id])){
				$presupuestos[$id] = $pres;
				array_push($ids, $id);
			}

			if(!isset($categorias[$id]))
				$categorias[$id] = array();
			
			if(!isset($subcategorias[$id]))
				$subcategorias[$id] = array();			
			
			$found = false;
			foreach($categorias[$id] as $cats){
				if($cats['cat_id'] == $cat['cat_id']){
					$found = true;
					break;
				}
			}
			if(!$found)
				array_push($categorias[$id], $cat);
						
			array_push($subcategorias[$id], $sub);

		}

		$results = array();
		foreach($ids as $id){
			foreach($categorias[$id] as $index => $cats){
				$cats['subcategorias'] = array();
				foreach($subcategorias[$id] as $subs){
					if($cats['cat_id'] == $subs['sub_cat']){
						$sub_found = false;
						foreach($cats['subcategorias'] as $catsub){
							if($catsub['sub_id'] == $subs['sub_id']){
								$sub_found = true;
								break;
							}
						}
						if(!$sub_found)
							array_push($cats['subcategorias'], $subs);
					}
				}
				$categorias[$id][$index]['subcategorias'] = $cats['subcategorias'];
			}
			array_push(
				$results, 
				array_merge(
					$presupuestos[$id], 
					array("categorias" => $categorias[$id])
				)
			);
		}
		return $results;

	}
    
	public function usuario_has_empresa($usuario, $empresa){
		global $wpdb;
		$query = "SELECT * FROM cu_users_has_empresas AS u WHERE u.empresa_id = $empresa AND u.user_id = $usuario;";
		$results = $wpdb->get_results($query, OBJECT);
		if(sizeof($results) > 0)
			return true;
		else
			return false;
	}
    
	public function empresa_has_presupuesto($empresa, $presupuesto){
		global $wpdb;
		$query = "SELECT * FROM cu_presupuestos WHERE empresa_id = $empresa AND id = $presupuesto;";
		$results = $wpdb->get_results($query, OBJECT);
		if(sizeof($results) > 0)
			return true;
		else
			return false;
	}
	
	public function get_presupuesto_data($presupuesto_id){
		global $wpdb;

		$query = "SELECT p.id, p.nombre, p.inicio, p.fin, p.ingreso, p.moneda, p.empresa_id, p.cuenta_id, cu.nombre as cuenta, c.nombre as cat_nombre, c.id as cat_id, pc.meta as cat_meta, pc.gasto as cat_gasto, pc.diferencia as cat_diferencia, s.id as sub_id, s.categoria_id as sub_cat, s.nombre as sub_nombre, pcs.meta as sub_meta, pcs.gasto as sub_gasto, pcs.diferencia as sub_diferencia
			FROM cu_presupuestos AS p
			LEFT JOIN cu_cuentas AS cu ON cu.id = p.cuenta_id
			INNER JOIN cu_presupuestos_has_categorias AS pc ON p.id = pc.presupuesto_id
			INNER JOIN cu_categorias AS c ON pc.categoria_id = c.id
			INNER JOIN cu_presupuestos_has_categorias_has_subcategorias AS pcs ON pcs.categoria_id = c.id
			INNER JOIN cu_subcategorias AS s ON pcs.subcategoria_id = s.id
			WHERE p.id = $presupuesto_id";
		$results = $wpdb->get_results($query, OBJECT);

		return $this->process_results($results);
	}

    public function list_all($usuario, $empresa, $busqueda_campo = null, $busqueda_valor = null, $orden_campo = null, $orden_valor = null, $filas = null, $pagina = null){
        global $wpdb;
        $busqueda = ($busqueda_campo == null || $busqueda_valor == null) ? "" : "AND $busqueda_campo LIKE '%$busqueda_valor%'";
        $orden = ($orden_campo == null || $orden_valor == null) ? "" : "ORDER BY $orden_campo $orden_valor";
        $limit = ($filas == null) ? "LIMIT 10" : "LIMIT $filas";

        if($usuario == null || !is_numeric($usuario) || !$this->usuario_has_empresa($usuario, $empresa)){
            return array("success" => false, "error" => "No estás autorizado");
        }

        $sql = "SELECT id FROM cu_presupuestos AS p WHERE p.empresa_id = $empresa $busqueda ";
        $wpdb->get_results($sql, OBJECT);

        $filas_total = $wpdb->num_rows;
        $paginas_total = ceil($filas_total / $filas);
        $offset = "OFFSET ".($pagina * $filas - $filas);

		$sql = "SELECT p.id, p.nombre, p.inicio, p.fin, p.ingreso, p.moneda, p.empresa_id, p.cuenta_id, cu.nombre as cuenta
			FROM cu_presupuestos AS p
			LEFT JOIN cu_cuentas AS cu ON cu.id = p.cuenta_id
			WHERE p.empresa_id = $empresa $busqueda $orden $limit $offset;";
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

    public function list_one($usuario, $empresa, $presupuesto){
        global $wpdb;
        
        if($usuario == null || !is_numeric($usuario) || !$this->usuario_has_empresa($usuario, $empresa)){
            return array(
				"success" => false, 
				"error" => "No estás autorizado"
			);
        }

        $sql = "SELECT p.id, p.nombre, p.inicio, p.fin, p.ingreso, p.moneda, p.empresa_id, p.cuenta_id, cu.nombre as cuenta, c.nombre as cat_nombre, c.id as cat_id, pc.meta as cat_meta, pc.gasto as cat_gasto, pc.diferencia as cat_diferencia, s.id as sub_id, s.categoria_id as sub_cat, s.nombre as sub_nombre, pcs.meta as sub_meta, pcs.gasto as sub_gasto, pcs.diferencia as sub_diferencia
			FROM cu_presupuestos AS p
			LEFT JOIN cu_cuentas AS cu ON cu.id = p.cuenta_id
			INNER JOIN cu_presupuestos_has_categorias AS pc ON p.id = pc.presupuesto_id
			INNER JOIN cu_categorias AS c ON pc.categoria_id = c.id
			INNER JOIN cu_presupuestos_has_categorias_has_subcategorias AS pcs ON pcs.categoria_id = c.id
			INNER JOIN cu_subcategorias AS s ON pcs.subcategoria_id = s.id
			WHERE p.empresa_id = $empresa
			AND p.id = $presupuesto;";
        $data = $wpdb->get_results($sql, OBJECT);
        return array(
            "success" => true,
            "data" =>$this->process_results($data),
        );

    }

    private function insert_presupuesto_categorias($presupuesto, $categorias){
        global $wpdb;
        $table = 'cu_presupuestos_has_categorias';
        $query = "INSERT INTO $table (presupuesto_id, categoria_id, meta) VALUES ";

        for($i=0; $i < sizeof($categorias); $i++){
            $cat = $categorias[$i];
            $cat_id = $cat['categoria_id'];
            $cat_meta = $cat['meta'];

            if($i == sizeof($categorias) - 1)
                $query = $query."($presupuesto, $cat_id, $cat_meta);";
            else
                $query = $query."($presupuesto, $cat_id, $cat_meta),";
        }

        $inserted = $wpdb->query($query);
        return $inserted;
    }

    private function update_presupuesto_categorias($presupuesto, $categorias){
        global $wpdb;
        $table = 'cu_presupuestos_has_categorias';

        for($i=0; $i < sizeof($categorias); $i++){
            $cat = $categorias[$i];
			$data = array(
				"meta" => $cat['meta'],
				"gasto" => $cat['gasto'],
				"diferencia" => $cat['diferencia'],
			);
            $cat_id = $cat['categoria_id'];
			$where = array(
				"presupuesto_id" => $presupuesto,
				"categoria_id" => $cat_id
			);
			$wpdb->update($table, $data, $where);            
        }
        return null;
    }

    private function insert_presupuesto_subcategorias($presupuesto, $subcategorias){
        global $wpdb;
        $table = 'cu_presupuestos_has_categorias_has_subcategorias';
        $query = "INSERT INTO $table (presupuesto_id, categoria_id, subcategoria_id, meta) VALUES ";

        if(empty($subcategorias))
            return false;
        
        for($i=0; $i < sizeof($subcategorias); $i++){
            $sub = $subcategorias[$i];
            $sub_id = $sub['subcategoria_id'];
            $sub_cat = $sub['categoria_id'];
            $sub_meta = $sub['meta'];

            if($i == sizeof($subcategorias) - 1)
                $query = $query."($presupuesto, $sub_cat, $sub_id, $sub_meta);";
            else
                $query = $query."($presupuesto, $sub_cat, $sub_id, $sub_meta),";
        }

        $inserted = $wpdb->query($query);
        return $inserted;
    }

    private function update_presupuesto_subcategorias($presupuesto, $subcategorias){
        global $wpdb;
        $table = 'cu_presupuestos_has_categorias_has_subcategorias';

        if(empty($subcategorias))
            return false;
        
        for($i=0; $i < sizeof($subcategorias); $i++){
			$sub = $subcategorias[$i];
			$sub_id = $sub['subcategoria_id'];
			$sub_cat = $sub['categoria_id'];

			$data = array(
				"meta" => $sub['meta'],
				"gasto" => $sub['gasto'],
				"diferencia" => $sub['diferencia']
			);
			$where = array(
				"presupuesto_id" => $presupuesto,
				"categoria_id" => $sub_cat,
				"subcategoria_id" => $sub_id
			);
			$wpdb->update($table, $data, $where);
        }

        return null;
    }

	public function create($usuario_id, $empresa_id, $presupuesto, $categorias, $subcategorias){
		global $wpdb;
		
		if($usuario_id == null || !is_numeric($usuario_id) || !$this->usuario_has_empresa($usuario_id, $empresa_id)){
			return array(
				"success" => false, 
				"error" => "No estás autorizado"
			);
        }
		
		$wpdb->insert('cu_presupuestos', $presupuesto);
		$presupuesto_id = $wpdb->insert_id;

        if($presupuesto_id > 0){
            $this->insert_presupuesto_categorias($presupuesto_id, $categorias);
            $this->insert_presupuesto_subcategorias($presupuesto_id, $subcategorias);
        }

		$data = $this->get_presupuesto_data($presupuesto_id);
		if(sizeof($data) > 0){
			return array("success" => true, "data" => $data);
		}
		else{
			return array("success" => false, "error" => "No se pudo crear la presupuesto");
		}
	}

	public function update($usuario_id, $empresa_id, $presupuesto, $categorias, $subcategorias){
		global $wpdb;
		
		if($usuario_id == null || !is_numeric($usuario_id) || !$this->usuario_has_empresa($usuario_id, $empresa_id)){
			return array(
				"success" => false, 
				"error" => "No estás autorizado"
			);
        }
		
		$wpdb->update('cu_presupuestos', $presupuesto);

		$this->update_presupuesto_categorias($presupuesto_id, $categorias);
        $this->update_presupuesto_subcategorias($presupuesto_id, $subcategorias);

		$data = $this->get_presupuesto_data($presupuesto_id);
		return array("success" => true, "data" => $data);
	}

	public function delete($usuario_id, $empresa_id, $presupuesto_id){
		global $wpdb;

		if($this->usuario_has_empresa($usuario_id, $empresa_id) && $this->empresa_has_presupuesto($empresa_id, $presupuesto_id)){
			$deleted = $wpdb->delete("cu_presupuestos_has_categorias_has_subcategorias", array("presupuesto_id" => $presupuesto_id));
			$deleted = $wpdb->delete("cu_presupuestos_has_categorias", array("presupuesto_id" => $presupuesto_id));
			$deleted = $wpdb->delete("cu_presupuestos", array("id" => $presupuesto_id));
			if($deleted > 0)
				return array("success" => true);
			else
				return array("success" => false, "error" => "No se pudo borrar el registro");
		}
		else{
			return array("success" => false, "error" => "No estás autorizado");
		}
	}
	
	public function update_saldo($presupuesto){
		global $wpdb;
		$query = "SELECT SUM(monto) as saldo FROM cu_registros WHERE presupuesto_id = $presupuesto";
		$results = $wpdb->get_results($query, OBJECT);
		$saldo = $results[0]->saldo;
		echo $wpdb->update('cu_presupuestos', array('saldo' => $saldo), array('id' => $presupuesto));
		return null;
	}

}