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
class Cuentas_Cuenta_Controllers {

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
        $this->_helpers = new Cuentas_Helpers( $this->plugin_name, $this->version );
        $this->_cuenta_models = new Cuentas_Cuenta_Models( $this->plugin_name, $this->version );

	}

    function load_dependencies(){

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'helpers/class-cuentas-helpers.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'models/class-cuentas-cuenta-models.php';

    }

    public function list_all($data){		
		return $this->_cuenta_models->list_all(
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
		return $this->_cuenta_models->list_one(
            $this->_helpers->get_user_id(),
			$data['empresa_id'],
            $data['cuenta_id'],
        );
	}

	public function create($request){
		$usuario = $this->_helpers->get_user_id();
		$empresa = $request['empresa_id'];		
		$data = $request->get_json_params();
		return $this->_cuenta_models->create($usuario, $empresa, $data);
	}

	public function update($request){
		$usuario_id = $this->_helpers->get_user_id();
		$cuenta_id = $request['cuenta_id'];
		$empresa_id = $request['empresa_id'];
		$data = $request->get_json_params();
		return $this->_cuenta_models->update($usuario_id, $empresa_id, $cuenta_id, $data);
	}

	public function delete($request){
		$cuenta_id = $request['cuenta_id'];
		$empresa_id = $request['empresa_id'];
		$usuario_id = $this->_helpers->get_user_id();

		return $this->_cuenta_models->delete($usuario_id, $empresa_id, $cuenta_id);
	}

}
