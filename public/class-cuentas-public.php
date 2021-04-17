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
class Cuentas_Public {

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

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cuentas_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cuentas_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cuentas-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cuentas_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cuentas_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cuentas-public.js', array( 'jquery' ), $this->version, false );

	}

	// LOGIN
	public function login($request){
		// Decoding
		$auth_head = explode("Basic ", $request->get_header('authorization'));
		$auth_decoded = base64_decode($auth_head[1]);
		$auth = explode(":", $auth_decoded);
		$username = $auth[0];
		$password = $auth[1];

		//Login In
		$creds = array();
		$creds['user_login'] = $username;
		$creds['user_password'] =  $password;
		$creds['remember'] = true;
		$user = wp_signon( $creds, true );

		if ( is_wp_error($user) )
		echo $user->get_error_message();

		$id = $user->ID;
		$meta = get_user_meta($id);

		return $meta;
	}

	public function logout(){
		return wp_logout();
	}

	// COOKIE VALIDATION
	public function valid_cookie($request){
		return wp_validate_auth_cookie( $_COOKIE[ LOGGED_IN_COOKIE ], 'logged_in' );
	}

	// EMPRESAS

	public function empresa_list_all(){
		return array('Aprendo','Feliz');
	}

	public function empresa_list_one($data){
		return $data->get_params();	
	}

	public function empresa_create(){
		global $wpdb;
		$results = $wpdb->get_results('SELECT * FROM wp_users', OBJECT);
		return $results;
	}

	public function empresa_update(){
		return array('Actualizado');
	}

	public function empresa_delete(){
		return array('Borrado');
	}

	//CUENTAS
	public function cuenta_list_all(){
		return array('Aprendo','Feliz');
	}

	public function cuenta_list_one($data){
		return $data->get_params();	
	}

	public function cuenta_create(){
		global $wpdb;
		$results = $wpdb->get_results('SELECT * FROM wp_users', OBJECT);
		return $results;
	}

	public function cuenta_update(){
		return array('Actualizado');
	}

	public function cuenta_delete(){
		return array('Borrado');
	}

	//REGISTROS
	public function registro_list_all(){
		return array('Aprendo','Feliz');
	}

	public function registro_list_one($data){
		return $data->get_params();	
	}

	public function registro_create(){
		global $wpdb;
		$results = $wpdb->get_results('SELECT * FROM wp_users', OBJECT);
		return $results;
	}

	public function registro_update(){
		return array('Actualizado');
	}

	public function registro_delete(){
		return array('Borrado');
	}

	

	public function add_cuentas_empresa_endpoints() {
		$namespace = 'cuentas/v1';
		$endpoints = array(
			array(
				'endpoint' => 'empresa/list',
				'method' => 'GET',
				'callback' => 'empresa_list_all'
			),
			array(
				'endpoint' => 'empresa/listOne',
				'method' => 'GET',
				'callback' => 'empresa_list_one'
			),
			array(
				'endpoint' => 'empresa/create',
				'method' => 'POST',
				'callback' => 'empresa_create'
			),
			array(
				'endpoint' => 'empresa/update',
				'method' => 'PUT',
				'callback' => 'empresa_update'
			),
			array(
				'endpoint' => 'empresa/delete',
				'method' => 'DELETE',
				'callback' => 'empresa_delete'
			),
			array(
				'endpoint' => 'cuenta/list',
				'method' => 'GET',
				'callback' => 'cuenta_list_all'
			),
			array(
				'endpoint' => 'cuenta/listOne',
				'method' => 'GET',
				'callback' => 'cuenta_list_one'
			),
			array(
				'endpoint' => 'cuenta/create',
				'method' => 'POST',
				'callback' => 'cuenta_create'
			),
			array(
				'endpoint' => 'cuenta/update',
				'method' => 'PUT',
				'callback' => 'cuenta_update'
			),
			array(
				'endpoint' => 'cuenta/delete',
				'method' => 'DELETE',
				'callback' => 'cuenta_delete'
			),
			array(
				'endpoint' => 'registro/list',
				'method' => 'GET',
				'callback' => 'registro_list_all'
			),
			array(
				'endpoint' => 'registro/listOne',
				'method' => 'GET',
				'callback' => 'registro_list_one'
			),
			array(
				'endpoint' => 'registro/create',
				'method' => 'POST',
				'callback' => 'registro_create'
			),
			array(
				'endpoint' => 'registro/update',
				'method' => 'PUT',
				'callback' => 'registro_update'
			),
			array(
				'endpoint' => 'registro/delete',
				'method' => 'DELETE',
				'callback' => 'registro_delete'
			),
		);
		foreach ($endpoints as $endpoint) {
			register_rest_route( 'cuentas/v1', $endpoint['endpoint'], array(
				'methods' => $endpoint['method'],
				'callback' => array($this, $endpoint['callback']),
				'permission_callback' => array($this, 'valid_cookie')
			) );
		}
		register_rest_route( 'cuentas/v1', '/login', array(
			'methods' => 'GET',
			'callback' => array($this, 'login')
			)
		);
		register_rest_route( 'cuentas/v1', '/logout', array(
			'methods' => 'GET',
			'callback' => array($this, 'logout')
			)
		);
	}
}
