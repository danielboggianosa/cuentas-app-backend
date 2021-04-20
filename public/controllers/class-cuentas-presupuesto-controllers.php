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
class Cuentas_Presupuesto_Controllers {

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
	 * La Clase de Helpers.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      class    $_helpers
	 */
	private $_helpers;

	/**
	 * La Clase de modelos de cuentas.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      class    $_cuenta_models
	 */
	private $_presupuesto_models;

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
        $this->_helpers = new Cuentas_Helpers( $this->plugin_name, $this->version );
        $this->_presupuesto_models = new Cuentas_Presupuesto_Models( $this->plugin_name, $this->version );

	}

    function load_dependencies(){

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/class-cuentas-helpers.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/class-cuentas-presupuesto-models.php';

    }

    public function list_all($data){		
		return $this->_presupuesto_models->list_all(
			$this->_helpers->get_user_id(),
			$data['empresa_id'],
            $data['busqueda_campo'],
            $data['busqueda_valor'],
            $data['orden_campo'],
            $data['orden_valor'],
            $data['filas'],
            $data['pagina']
        );
		
	}
	
	public function list_one($data){		
		return $this->_presupuesto_models->list_one(
            $this->_helpers->get_user_id(),
			$data['empresa_id'],
            $data['presupuesto_id'],
        );
	}

	public function create($request){
		$usuario = $this->_helpers->get_user_id();
		$body = $request->get_json_params();
        $presupuesto = $body['presupuesto'];
		$empresa = $presupuesto['empresa_id'];		
        $categorias = $body['categorias'];
        $subcategorias = $body['subcategorias'];
		return $this->_presupuesto_models->create($usuario, $empresa, $presupuesto, $categorias, $subcategorias);
	}

	public function update($request){
		$usuario_id = $this->_helpers->get_user_id();
		$presupuesto_id = $request['presupuesto_id'];
		$empresa_id = $request['empresa_id'];
		$data = $request->get_json_params();
		return $this->_presupuesto_models->update($usuario_id, $empresa_id, $presupuesto_id, $data);
	}

	public function delete($request){
		$presupuesto_id = $request['presupuesto_id'];
		$empresa_id = $request['empresa_id'];
		$usuario_id = $this->_helpers->get_user_id();

		return $this->_presupuesto_models->delete($usuario_id, $empresa_id, $presupuesto_id);
	}

}
