<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://javmah.tk/WordPress post to Trello card 
 * @since      1.0.0
 *
 * @package    Wp_Post_To_Trello_Card
 * @subpackage Wp_Post_To_Trello_Card/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Post_To_Trello_Card
 * @subpackage Wp_Post_To_Trello_Card/includes
 * @author     javmah <jaedmah@gmail.com>
 */
class Wp_Post_To_Trello_Card {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Post_To_Trello_Card_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_POST_TO_TRELLO_CARD_VERSION' ) ) {
			$this->version = WP_POST_TO_TRELLO_CARD_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-post-to-trello-card';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Post_To_Trello_Card_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Post_To_Trello_Card_i18n. Defines internationalization functionality.
	 * - Wp_Post_To_Trello_Card_Admin. Defines all hooks for the admin area.
	 * - Wp_Post_To_Trello_Card_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-post-to-trello-card-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-post-to-trello-card-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-post-to-trello-card-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-post-to-trello-card-public.php';

		$this->loader = new Wp_Post_To_Trello_Card_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Post_To_Trello_Card_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Post_To_Trello_Card_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wp_Post_To_Trello_Card_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		# Admin menu and admin Notice 
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'bptc_menu_pages' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'bptc_admin_notice' );
		# Wp Form Action || Saving bptc access code
		$this->loader->add_action( 'admin_post_bptcaccess_code', $plugin_admin,'bptc_access_code' );
		$this->loader->add_action( 'admin_post_nopriv_bptcaccess_code', $plugin_admin, 'bptc_access_code' );
		# Wp Form Action || Saving bptc Settings 
		$this->loader->add_action( 'admin_post_bptc_settings', $plugin_admin,'bptc_settings' );
		$this->loader->add_action( 'admin_post_nopriv_bptc_settings', $plugin_admin, 'bptc_settings' );
		# AJAX Calls
		$this->loader->add_action('wp_ajax_bptc_ajax_response', $plugin_admin,'bptc_ajax_response');
		$this->loader->add_action('wp_ajax_nopriv_bptc_ajax_response', $plugin_admin,'bptc_ajax_response');
		# POST  status pchanges
		$this->loader->add_action('transition_post_status', $plugin_admin,'bptc_on_all_post_status_transitions', 10, 3);
		# publish_post
		$this->loader->add_action( 'publish_post', $plugin_admin, 'bptc_post_published_notification', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Post_To_Trello_Card_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Post_To_Trello_Card_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

