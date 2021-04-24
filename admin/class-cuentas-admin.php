<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.danielboggiano.com
 * @since      1.0.0
 *
 * @package    Cuentas
 * @subpackage Cuentas/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cuentas
 * @subpackage Cuentas/admin
 * @author     Daniel Boggiano <dev@danielboggiano.com>
 */
class Cuentas_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sb_styles.css', array(), $this->version, 'all' );
		
		wp_register_style('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css');
		wp_enqueue_style('prefix_bootstrap');
		wp_register_style('font_awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
		wp_enqueue_style('font_awesome');

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cuentas-admin.js', array( 'jquery' ), $this->version, false );

		wp_register_script('prefix_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js');
		wp_enqueue_script('prefix_bootstrap');

		wp_register_script('bundle_bootstrap', '//cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js');
		wp_enqueue_script('bundle_bootstrap');

	}

	public function remove_menus(){
		remove_menu_page( 'index.php' );                  //Dashboard
		remove_menu_page( 'edit.php' );                   //Posts
		remove_menu_page( 'upload.php' );                 //Media
		remove_menu_page( 'edit.php?post_type=page' );    //Pages
		remove_menu_page( 'edit-comments.php' );          //Comments
		remove_menu_page( 'themes.php' );                 //Appearance
		remove_menu_page( 'tools.php' );                  //Tools
		remove_menu_page( 'options-general.php' );        //Settings
		// remove_menu_page( 'jetpack' );                    //Jetpack* 
		//remove_menu_page( 'plugins.php' );                //Plugins
		//remove_menu_page( 'users.php' );                //Users
		// remove_action( 'admin_notices', 'update_nag', 3 ); //Update Message
	 }

	public function remove_wp_logo( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'wp-logo' );
		$wp_admin_bar->remove_node( 'site-name' );    
		$wp_admin_bar->remove_menu( 'comments' );    
		$wp_admin_bar->remove_node( 'new-post' );
		$wp_admin_bar->remove_node( 'new-media' );
		$wp_admin_bar->remove_node( 'new-page' );    
	}

	function add_menu_page() {
		$usuario = array(
			'suscriptor' => 'read', 
			'colaborador' => 'edit_posts', 
			'autor' => 'upload_files', 
			'editor' => 'manage_categories', 
			'administrador' => 'manage_options' 
		);
		add_menu_page(
			'Inicio',
			'Inicio',
			$usuario["suscriptor"], 
			'mm-index', 
			array($this, 'mm_index'),
			'dashicons-admin-home',
			1
		);
		add_menu_page(
			'Empresas',
			'Empresas',
			$usuario["suscriptor"], 
			'mm-empresas', 
			array($this, 'mm_empresas'),
			'dashicons-store',
			8
		);
		add_menu_page(
			'Cuentas',
			'Cuentas',
			$usuario["suscriptor"], 
			'mm-cuentas', 
			array($this, 'mm_cuentas'),
			'dashicons-media-spreadsheet',
			9
		);
		add_menu_page(
			'Registros',
			'Registros',
			$usuario["suscriptor"], 
			'mm-registros', 
			array($this, 'mm_registros'),
			'dashicons-list-view',
			10
		);
		add_menu_page(
			'Presupuestos',
			'Presupuestos',
			$usuario["suscriptor"], 
			'mm-presupuestos', 
			array($this, 'mm_presupuestos'),
			'dashicons-list-view',
			10
		);
		
	}
	function mm_index(){
		include "partials/inicio.php";
	}
	function mm_empresas(){
		include "partials/empresas.php";
	}
	function mm_cuentas(){
		include "partials/cuentas.php";
	}
	function mm_registros(){
		include "partials/registros.php";
	}
	function mm_presupuestos(){
		include "partials/presupuestos.php";
	}
		
	function add_subpage( $wp_admin_bar ){
		$args = array(
			'id'    => 'print',
			'title' => '<span class="ab-icon dashicons dashicons-media-text"></span><span class="ab-lable">Imprimir</span>',
			'href' => '#',
			'meta'  => array( 
						'class' => 'ab-item',
						'onclick' => 'print()',
							)
		);
		$wp_admin_bar->add_node( $args );
		$args = array(
			'id'    => 'send-mail',
			'title' => '<span class="ab-icon dashicons dashicons-email-alt"></span> Enviar por Correo',
			'href' => site_url()."/wp-admin/admin.php?page=mm-index",
			'meta'  => array( 'class' => 'my-toolbar-page' )
		);
		$wp_admin_bar->add_node( $args );
		$args = array(
			'parent' => 'new-content',
			'id'    => 'new-business',
			'title' => 'Empresa',
			'href'  => site_url()."/wp-admin/admin.php?page=mm-empresas&empresa=nuevo",
			'meta'  => array( 'class' => 'my-toolbar-page' )
		);
		$wp_admin_bar->add_node( $args );
	}

}
