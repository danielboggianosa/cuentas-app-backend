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
			return array("success" => false, "message" => "No autorizado");
		// echo $user->get_error_message();

		$id = $user->ID;
		$meta = get_user_meta($id);

		return array("success" => true, "data" => $meta);
	}

    // LOGOUT
	public function logout(){
		return wp_logout();
	}

	// COOKIE VALIDATION
	public function valid_cookie(){
		return wp_validate_auth_cookie( $_COOKIE[ LOGGED_IN_COOKIE ], 'logged_in' );
	}

	public function add_endpoints() {
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
