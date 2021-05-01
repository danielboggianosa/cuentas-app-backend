<?php

use \Firebase\JWT\JWT;

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
class Cuentas_Auth {

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

	private function generate_jwt_token($user){
		$key = JWT_AUTH_SECRET_KEY;
		$payload = array(
			"iss" => get_site_url(),
			"iat" => 1356999524,
			"ext" => time() + 100000,
			"sub" => $user->ID,
			"user" => $user->nickname
		);

		return JWT::encode($payload, $key);
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
		$creds['remember'] = false;
		$user = wp_signon( $creds, true );

		if ( is_wp_error($user) )
			return array("success" => false, "message" => "No autorizado");
		// echo $user->get_error_message();

		$id = $user->ID;
		$meta = get_user_meta($id);

		$jwt = $this->generate_jwt_token($user);

		return array("success" => true, "data" => $meta, "token" => $jwt);
	}

    // LOGOUT
	public function logout(){
		return wp_logout();
	}

	// COOKIE VALIDATION
	public function valid_cookie(){
		return wp_validate_auth_cookie( $_COOKIE[ LOGGED_IN_COOKIE ], 'logged_in' );
	}

	// VALIDATE TOKEN
	public function validate_token($request){
		header("Access-Control-Allow-Headers: Authorization, token");
		header("Access-Control-Allow-Origin: *");
		if($request->get_header('token') == '')
			return $this->valid_cookie();
		else{
			$key = JWT_AUTH_SECRET_KEY;
			// $bearer = $request->get_header('Authorization');
			// echo $bearer;
			// $token = explode("Bearer ", $bearer);
			$token = $request->get_header('token');
			if($token != ''){
				$decoded = JWT::decode($token, $key, array('HS256'));
				return ( $decoded->ext > time() ) ? true : false;
			}
			else return false;
	
		}

	}

	public function add_endpoints() {
		register_rest_route( 'cuentas/v1', '/login', array(
			'methods' => 'GET',
			'callback' => array($this, 'login'),
			'permission_callback' => array()
			)
		);
		register_rest_route( 'cuentas/v1', '/logout', array(
			'methods' => 'GET',
			'callback' => array($this, 'logout'),
			'permission_callback' => array()
			)
		);
	}
}
