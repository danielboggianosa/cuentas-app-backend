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
	public function valid_cookie(){
		return wp_validate_auth_cookie( $_COOKIE[ LOGGED_IN_COOKIE ], 'logged_in' );
	}

	// HELPERS

	private function get_user_id(){
		return wp_validate_auth_cookie( $_COOKIE[ LOGGED_IN_COOKIE ], 'logged_in' );
	}

	private function usuario_has_empresa($usuario_id, $empresa_id){
		global $wpdb;
		$query = "SELECT * FROM mm_empresa_usuario WHERE mm_empresa_id = $empresa_id AND mm_usuario_id = $usuario_id;";
		$results = $wpdb->get_results($query, OBJECT);
		return $results;
		if(sizeof($results) > 0)
			return true;
		else
			return false;
	}

	// EMPRESAS

	public function empresa_list_all($data){
		global $wpdb;
		$limit = " LIMIT ".$data['limit'];
		$field = (isset($data['field']) && !empty($data['field'])) ? ' AND '.$data['field'] : "";
		$value = $data['value'] ? "LIKE '%".$data['value']."%'" : "";
		$usuario_id = $this->get_user_id();
		$query = "SELECT e.mm_empresa_id as id, e.mm_empresa_nombre as nombre, e.mm_empresa_notas as notas, e.mm_empresa_foto as foto 
		FROM mm_empresa AS e 
		INNER JOIN mm_empresa_usuario AS eu ON e.mm_empresa_id = eu.mm_empresa_id 
		WHERE eu.mm_usuario_id = ".$usuario_id.$field.$value.$limit.";";
		$results = $wpdb->get_results($query, OBJECT);
		$success = ($results) ? true : false;
		return array("success" => $success, "data" => $results);
	}

	public function empresa_list_one($data){
		global $wpdb;
		$empresa_id = $data['id'];
		$usuario_id = $this->get_user_id();
		$query = "SELECT e.mm_empresa_id as id, e.mm_empresa_nombre as nombre, e.mm_empresa_notas as notas, e.mm_empresa_foto as foto FROM mm_empresa AS e INNER JOIN mm_empresa_usuario AS eu ON e.mm_empresa_id = eu.mm_empresa_id WHERE e.mm_empresa_id = $empresa_id LIMIT 1";
		$results = $wpdb->get_results($query, OBJECT);
		$success = ($results) ? true : false;
		return array("success" => $success, "data" => $results);
	}

	public function empresa_create($data){
		global $wpdb;
		$empresa = 'mm_empresa';
		$empresa_usuario = 'mm_empresa_usuario';
		$usuario_id = $this->get_user_id();
		$wpdb->insert($empresa, array(
			$empresa."_nombre" => $data['nombre'],
			$empresa."_notas" => $data['notas'],
			$empresa."_foto" => $data['foto']
		));
		$empresa_id = $wpdb->insert_id;
		$wpdb->insert($empresa_usuario, array(
			"mm_empresa_id" => $empresa_id,
			"mm_usuario_id" => $usuario_id
		));
		return array("id"=>$empresa_id);
	}

	private function get_empresa_data($empresa_id, $usuario_id){
		global $wpdb;

		$query = "SELECT e.mm_empresa_id as id, e.mm_empresa_nombre as nombre, e.mm_empresa_notas as notas, e.mm_empresa_foto as foto FROM mm_empresa AS e INNER JOIN mm_empresa_usuario AS eu ON e.mm_empresa_id = eu.mm_empresa_id WHERE e.mm_empresa_id = $empresa_id LIMIT 1";
		
		$results = $wpdb->get_results($query, OBJECT);
		return $results[0];
	}

	public function empresa_update($data){
		global $wpdb;
		$empresa_id = $data['id'];
		$empresa_nombre = $data['nombre'];
		$empresa_notas = $data['notas'];
		$empresa_foto = $data['foto'];

		$usuario_id = $this->get_user_id();
		$empresa = $this->get_empresa_data($empresa_id, $usuario_id);

		if($empresa){
			$result = $wpdb->update(
				'mm_empresa', 
				array(
					"mm_empresa_nombre" => $data['nombre'],
					"mm_empresa_notas" => $data['notas'],
					"mm_empresa_foto" => $data['foto']
				), 
				array("mm_empresa_id" => $empresa_id)
			);
			return array(
				"success" => true,
				"data" => array(
					"old_data" => $empresa,
					"new_data" => $this->get_empresa_data($empresa_id, $usuario_id),
				)
			);				
		}
		else{
			return array("success" => false);
		}

		
	}

	public function empresa_add_user($data){
		global $wpdb;
		$empresa_id = $data['empresa_id'];
		$usuario_id = $this->get_user_id();

		$empresa = $this->get_empresa_data($empresa_id, $usuario_id);
		$user = get_user_by( 'email', $data['usuario_email'] );
		$user_id = $user->data->ID;
		
		if($empresa && $user_id){			
			$result = $wpdb->insert(
				'mm_empresa_usuario', 
				array(
					"mm_empresa_id" => $empresa_id,
					"mm_usuario_id" => $user_id,
				)
			);
			return array( "success" => true );				
		}
		else{
			return array( "success" => false );
		}
		
	}

	public function empresa_delete($data){
		global $wpdb;
		$empresa_id = $data['id'];

		$usuario_id = $this->get_user_id();
		$empresa = $this->get_empresa_data($empresa_id, $usuario_id);

		if($empresa){
			$result = $wpdb->delete( 'mm_empresa', array("mm_empresa_id" => $empresa_id) );
			$result = $wpdb->delete( 'mm_empresa_usuario', array("mm_empresa_id" => $empresa_id) );
			return array("success" => $result);				
		}
		else{
			return array("success" => false);
		}
	}

	//CUENTAS
	public function cuenta_list_all($data){
		global $wpdb;
		$limit = " LIMIT ".$data['limit'];
		$field = (isset($data['field']) && !empty($data['field'])) ? ' AND '.$data['field'] : "";
		$value = $data['value'] ? "LIKE '%".$data['value']."%'" : "";
		$empresa_id = $data['empresa_id'];
		$usuario_id = $this->get_user_id();
		$query = "SELECT mm_cuenta_id as id, mm_cuenta_nombre as nombre, mm_cuenta_banco as banco, mm_cuenta_moneda as moneda, mm_cuenta_numero as numero, mm_cuenta_cci as cci, mm_cuenta_foto as foto, mm_cuenta_empresa_id as empresa_id, mm_empresa_nombre as empresa_nombre  FROM mm_cuenta AS c INNER JOIN mm_empresa AS e ON c.mm_cuenta_empresa_id = e.mm_empresa_id INNER JOIN mm_empresa_usuario AS eu ON e.mm_empresa_id = eu.mm_empresa_id WHERE e.mm_empresa_id = $empresa_id AND eu.mm_usuario_id = ".$usuario_id.$field.$value.$limit.";";
		$results = $wpdb->get_results($query, OBJECT);
		$success = ($results) ? true : false;
		return array("success" => $success, "data" => $results);
	}

	public function cuenta_list_one($data){
		global $wpdb;
		$cuenta_id = $data['cuenta_id'];
		$usuario_id = $this->get_user_id();
		$query = "SELECT mm_cuenta_id as id, mm_cuenta_nombre as nombre, mm_cuenta_banco as banco, mm_cuenta_moneda as moneda, mm_cuenta_numero as numero, mm_cuenta_cci as cci, mm_cuenta_foto as foto, mm_cuenta_empresa_id as empresa_id, mm_empresa_nombre as empresa_nombre  FROM mm_cuenta AS c INNER JOIN mm_empresa AS e ON c.mm_cuenta_empresa_id = e.mm_empresa_id INNER JOIN mm_empresa_usuario AS eu ON e.mm_empresa_id = eu.mm_empresa_id WHERE c.mm_cuenta_id = $cuenta_id AND eu.mm_usuario_id = ".$usuario_id.";";
		$results = $wpdb->get_results($query, OBJECT);
		$success = ($results) ? true : false;
		return array("success" => $success, "data" => $results);
	}

	public function cuenta_create($data){
		global $wpdb;
		$cuenta = 'mm_cuenta';
		$empresa_id = $data['empresa_id'];
		$usuario_id = $this->get_user_id();

		if($this->usuario_has_empresa($usuario_id, $empresa_id)){
			$result = $wpdb->insert($cuenta, array(
				$cuenta."_empresa_id" => $empresa_id,
				$cuenta."_nombre" => $data['nombre'],
				$cuenta."_banco" => $data['banco'],
				$cuenta."_moneda" => $data['moneda'],
				$cuenta."_numero" => $data['numero'],
				$cuenta."_cci" => $data['cci'],
				$cuenta."_notas" => $data['notas'],
				$cuenta."_foto" => $data['foto']
			));
			$cuenta_id = $wpdb->insert_id;
			$data = $this->cuenta_list_one(array('cuenta_id' => $cuenta_id));
			return array("success" => true, "data" => $data['data'], "insertados" => $result);
		}
		else{
			return array("success" => false, "data" => $this->usuario_has_empresa($usuario_id, $empresa_id));
		}

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

	//CATEGORÍAS
	public function categoria_list_all(){
		return array('Aprendo','Feliz');
	}

	public function categoria_list_one($data){
		return $data->get_params();	
	}

	public function categoria_create(){
		global $wpdb;
		$results = $wpdb->get_results('SELECT * FROM wp_users', OBJECT);
		return $results;
	}

	public function categoria_update(){
		return array('Actualizado');
	}

	public function categoria_delete(){
		return array('Borrado');
	}

	//SUBCATEGORÍAS
	public function subcategoria_list_all(){
		return array('Aprendo','Feliz');
	}

	public function subcategoria_list_one($data){
		return $data->get_params();	
	}

	public function subcategoria_create(){
		global $wpdb;
		$results = $wpdb->get_results('SELECT * FROM wp_users', OBJECT);
		return $results;
	}

	public function subcategoria_update(){
		return array('Actualizado');
	}

	public function subcategoria_delete(){
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
				'endpoint' => 'empresa/addUser',
				'method' => 'POST',
				'callback' => 'empresa_add_user'
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
			array(
				'endpoint' => 'categoria/list',
				'method' => 'GET',
				'callback' => 'categoria_list_all'
			),
			array(
				'endpoint' => 'categoria/listOne',
				'method' => 'GET',
				'callback' => 'categoria_list_one'
			),
			array(
				'endpoint' => 'categoria/create',
				'method' => 'POST',
				'callback' => 'categoria_create'
			),
			array(
				'endpoint' => 'categoria/update',
				'method' => 'PUT',
				'callback' => 'categoria_update'
			),
			array(
				'endpoint' => 'categoria/delete',
				'method' => 'DELETE',
				'callback' => 'categoria_delete'
			),
			array(
				'endpoint' => 'subcategoria/list',
				'method' => 'GET',
				'callback' => 'subcategoria_list_all'
			),
			array(
				'endpoint' => 'subcategoria/listOne',
				'method' => 'GET',
				'callback' => 'subcategoria_list_one'
			),
			array(
				'endpoint' => 'subcategoria/create',
				'method' => 'POST',
				'callback' => 'subcategoria_create'
			),
			array(
				'endpoint' => 'subcategoria/update',
				'method' => 'PUT',
				'callback' => 'subcategoria_update'
			),
			array(
				'endpoint' => 'subcategoria/delete',
				'method' => 'DELETE',
				'callback' => 'subcategoria_delete'
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
