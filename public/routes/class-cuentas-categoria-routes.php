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
class Cuentas_Categoria_Routes {

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
        $this->load_dependencies();

	}

    function load_dependencies(){

        require_once plugin_dir_path( dirname( __FILE__ ) ) . '/auth/class-cuentas-auth.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . '/controllers/class-cuentas-categoria-controllers.php';

    }

	public function add_endpoints() {
        $_auth = new Cuentas_Auth( $this->plugin_name, $this->version) ;
        $_categoria_controllers = new Cuentas_Categoria_Controllers( $this->plugin_name, $this->version) ;

		$namespace = 'cuentas/v1';
		$endpoints = array(
			array(
				'endpoint' => 'categoria/(?P<empresa_id>\d+)',
				'method' => 'GET',
				'callback' => 'list_all',
				'permission' => 'user_can_read'
			),
			array(
				'endpoint' => 'categoria/(?P<empresa_id>\d+)/(?P<categoria_id>\d+)',
				'method' => 'GET',
				'callback' => 'list_one',
				'permission' => 'user_can_read'
			),
			array(
				'endpoint' => 'categoria',
				'method' => 'POST',
				'callback' => 'create',
				'permission' => 'user_can_create'
			),
			array(
				'endpoint' => 'categoria/(?P<empresa_id>\d+)/(?P<categoria_id>\d+)',
				'method' => 'PUT',
				'callback' => 'update',
				'permission' => 'user_can_update'
			),
			array(
				'endpoint' => 'categoria/(?P<empresa_id>\d+)/(?P<categoria_id>\d+)',
				'method' => 'DELETE',
				'callback' => 'delete',
				'permission' => 'user_can_delete'
			),
		);
		foreach ($endpoints as $e) {
			register_rest_route( $namespace, $e['endpoint'], array(
				'methods' => $e['method'],
				'callback' => array($_categoria_controllers, $e['callback']),
				'permission_callback' => array($_auth, 'valid_cookie')
			) );
		}
	}
}
