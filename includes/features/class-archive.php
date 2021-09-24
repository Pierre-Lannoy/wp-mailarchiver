<?php
/**
 * MailArchiver archiver utilities.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use Mailarchiver\Plugin\Feature\DArchiver;
use MAMonolog\Logger;
use Mailarchiver\Plugin\Feature\EventTypes;


/**
 * Utilities MailArchiver class.
 *
 * This class defines all code necessary to log events with MailArchiver.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class Archive {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since   1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Get a new archiver instance.
	 *
	 * @param   string $class The class identifier, see Mailarchiver\API\DArchiver::$classes.
	 * @param   string $name Optional. The name of the component.
	 * @param   string $version Optional. The version of the component.
	 * @param   string $test Optional. The handler to bootstrap if specified..
	 * @return  DArchiver The MailArchiver archiver instance.
	 * @since   1.0.0
	 */
	public static function bootstrap( $class, $name = null, $version = null, $test = null ) {
		return new DArchiver( $class, $name, $version, $test );
	}

	/**
	 * Get a level name.
	 *
	 * @param   integer $level The level value.
	 * @return  string The level name.
	 * @since   1.0.0
	 */
	public static function level_name( $level ) {
		$result = 'UNKNOWN';
		if ( Logger::INFO === (int) $level ) {
			$result = esc_html__( 'All emails', 'mailarchiver' );
		}
		if ( Logger::ERROR === (int) $level ) {
			$result = esc_html__( 'Only emails in error', 'mailarchiver' );
		}
		return $result;
	}

	/**
	 * Get the levels list.
	 *
	 * @param   integer $minimal    optional. The minimal level to add.
	 * @return  array The level list.
	 * @since   1.0.0
	 */
	public static function get_levels( $minimal = Logger::INFO ) {
		$result = [];
		foreach ( EventTypes::$level_names as $key => $name ) {
			if ( $key >= $minimal ) {
				if ( Logger::INFO === $key || Logger::ERROR === $key ) {
					if ( 'INFO' === $name ) {
						$result[] = [ $key, esc_html__( 'All emails', 'mailarchiver' ) ];
					}
					if ( 'ERROR' === $name ) {
						$result[] = [ $key, esc_html__( 'Only emails in error', 'mailarchiver' ) ];
					}
				}
			}
		}
		return $result;
	}

}
