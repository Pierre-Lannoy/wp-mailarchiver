<?php
/**
 * Security options handling
 *
 * Handles all available security options.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.11.0
 */

namespace Mailarchiver\Plugin\Feature;

/**
 * Define the security options functionality.
 *
 * Handles all available security options.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.11.0
 */
class SecurityOptions {

	/**
	 * The list of available options.
	 *
	 * @since  2.11.0
	 * @var    array    $options    Maintains the options list.
	 */
	public static $options = [ 'xss' ];

	/**
	 * The list of options names.
	 *
	 * @since  2.11.0
	 * @var    array    $options_names    Maintains the options names list.
	 */
	public static $options_names = [];

	/**
	 * The list of options icons.
	 *
	 * @since  2.11.0
	 * @var    array    $options_icons    Maintains the options icons list.
	 */
	public static $options_icons = [];

	/**
	 * Initialize the meta class and set its properties.
	 *
	 * @since    2.11.0
	 */
	public static function init() {
		self::$options_names['xss'] = esc_html__( 'XSS protection', 'mailarchiver' );
		self::$options_icons['xss'] = esc_html__( 'shield' );
	}

}

SecurityOptions::init();
