<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://javmah.tk/WordPress post to Trello card 
 * @since      1.0.0
 *
 * @package    Wp_Post_To_Trello_Card
 * @subpackage Wp_Post_To_Trello_Card/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Post_To_Trello_Card
 * @subpackage Wp_Post_To_Trello_Card/includes
 * @author     javmah <jaedmah@gmail.com>
 */
class Wp_Post_To_Trello_Card_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-post-to-trello-card',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
