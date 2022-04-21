<?php
/**
 * Privacy options handling
 *
 * Handles all available privacy options.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.8.0
 */

namespace Mailarchiver\Plugin\Feature;

/**
 * Define the privacy options functionality.
 *
 * Handles all available privacy options.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.8.0
 */
class PrivacyOptions {

	/**
	 * The list of available options.
	 *
	 * @since  2.8.0
	 * @var    array    $options    Maintains the options list.
	 */
	public static $options = [ 'obfuscation', 'pseudonymization', 'mailanonymization', 'encryption' ];

	/**
	 * The list of options names.
	 *
	 * @since  2.8.0
	 * @var    array    $options_names    Maintains the options names list.
	 */
	public static $options_names = [];

	/**
	 * The list of options icons.
	 *
	 * @since  2.8.0
	 * @var    array    $options_icons    Maintains the options icons list.
	 */
	public static $options_icons = [];

	/**
	 * Initialize the meta class and set its properties.
	 *
	 * @since    2.8.0
	 */
	public static function init() {
		self::$options_names['obfuscation']       = esc_html__( 'Obfuscation', 'mailarchiver' );
		self::$options_names['pseudonymization']  = esc_html__( 'Pseudonymization', 'mailarchiver' );
		self::$options_names['mailanonymization'] = esc_html__( 'Masking', 'mailarchiver' );
		self::$options_names['encryption']        = esc_html__( 'Encryption', 'mailarchiver' );
		self::$options_icons['obfuscation']       = esc_html__( 'eye' );
		self::$options_icons['pseudonymization']  = esc_html__( 'user' );
		self::$options_icons['mailanonymization'] = esc_html__( 'mail' );
		self::$options_icons['encryption']        = esc_html__( 'lock' );
	}

}

PrivacyOptions::init();
