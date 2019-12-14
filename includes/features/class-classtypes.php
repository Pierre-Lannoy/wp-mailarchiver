<?php
/**
 * Class types handling
 *
 * Handles all available class types.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

/**
 * Define the class types functionality.
 *
 * Handles all available class types.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class ClassTypes {

	/**
	 * The list of available classes.
	 *
	 * @since  1.0.0
	 * @var    array    $classes    Maintains the classes list.
	 */
	public static $classes = [ 'mail', 'plugin', 'theme', 'mu', 'unknown' ];

	/**
	 * The list of classes names.
	 *
	 * @since  1.0.0
	 * @var    array    $classe_names    Maintains the classes list.
	 */
	public static $classe_names = [];

	/**
	 * Initialize the meta class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public static function init() {
		self::$classe_names['mail']    = esc_html__( 'Core', 'mailarchiver' );
		self::$classe_names['plugin']  = esc_html__( 'Plugin', 'mailarchiver' );
		self::$classe_names['theme']   = esc_html__( 'Theme', 'mailarchiver' );
		self::$classe_names['mu']      = esc_html__( 'MU-Plugin', 'mailarchiver' );
		self::$classe_names['unknown'] = esc_html__( 'Unknown', 'mailarchiver' );
	}

}

ClassTypes::init();
