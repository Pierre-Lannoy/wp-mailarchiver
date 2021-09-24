<?php
/**
 * Event types handling
 *
 * Handles all available event types.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use MAMonolog\Logger;
use Feather;

/**
 * Define the event types functionality.
 *
 * Handles all available event types.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class EventTypes {

	/**
	 * List of the available levels.
	 *
	 * @since    1.0.0
	 * @var string[] $levels Logging levels.
	 */
	public static $levels = [
		'info'  => Logger::INFO,
		'error' => Logger::ERROR,
	];

	/**
	 * List of the levels colors.
	 *
	 * @since    3.0.0
	 * @var string[] $levels_colors Logging levels colors.
	 */
	public static $levels_colors = [
		'info'  => [ '#EEEEFF', '#9999FF' ],
		'error' => [ '#FFD2A8', '#FB7B00' ],
	];

	/**
	 * List of the available icons.
	 *
	 * @since    1.0.0
	 * @var string[] $icons Logging levels.
	 */
	public static $icons = [];

	/**
	 * List of the available level texts.
	 *
	 * @var string[] $level_names Logging levels texts.
	 */
	public static $level_texts = [];

	/**
	 * List of the available level names.
	 *
	 * @var string[] $level_names Logging levels names.
	 */
	public static $level_names = [
		Logger::INFO  => 'INFO',
		Logger::ERROR => 'ERROR',
	];

	/**
	 * List of the available levels.
	 *
	 * @since    1.0.0
	 * @var string[] $level_values Logging levels.
	 */
	public static $level_values = [
		Logger::INFO,
		Logger::ERROR,
	];

	/**
	 * Initialize the meta class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public static function init() {
		self::$icons                  = [];
		self::$icons['unknown']       = Feather\Icons::get_base64( 'mail', '#F0F0F0', '#CCCCCC' );
		self::$icons['info']          = Feather\Icons::get_base64( 'mail', '#DDDDFF', '#5555FF' );
		self::$icons['error']         = Feather\Icons::get_base64( 'mail', '#FFB7B7', '#DD0000' );
		self::$level_texts            = [];
		self::$level_texts['unknown'] = esc_html__( 'The sent status of the email is unknown.', 'mailarchiver' );
		self::$level_texts['info']    = esc_html__( 'The email was successfully sent.', 'mailarchiver' );
		self::$level_texts['error']   = esc_html__( 'The email was not sent, an error was generated.', 'mailarchiver' );
	}

}

EventTypes::init();
