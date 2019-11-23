<?php
/**
 * Archiver maintenance handling
 *
 * Handles all archiver maintenance operations.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use Mailarchiver\System\Option;

/**
 * Define the archiver maintenance functionality.
 *
 * Handles all archiver maintenance operations.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class ArchiverMaintainer {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}

	/**
	 * Create an instance of $class_name.
	 *
	 * @param   string $class_name The class name.
	 * @param   array  $args   The param of the constructor for $class_name class.
	 * @return  boolean|object The instance of the class if creation was possible, null otherwise.
	 * @since    1.0.0
	 */
	private function create_instance( $class_name, $args = [] ) {
		if ( class_exists( $class_name ) ) {
			try {
				$reflection = new \ReflectionClass( $class_name );
				return $reflection->newInstanceArgs( $args );
			} catch ( \Exception $e ) {
				return false;
			}
		}
		return false;
	}

	/**
	 * Clean the archiver.
	 *
	 * @since    1.0.0
	 */
	public function cron_clean() {
		foreach ( Option::network_get( 'archivers' ) as $key => $archiver ) {
			$classname = 'Mailarchiver\Plugin\Feature\\' . $archiver['handler'];
			if ( class_exists( $classname ) ) {
				$archiver['uuid'] = $key;
				$instance         = $this->create_instance( $classname );
				$instance->set_archiver( $archiver );
				$instance->cron_clean();
			}
		}
	}

	/**
	 * Update the archiver.
	 *
	 * @since    1.0.0
	 */
	public function update( $from ) {
		foreach ( Option::network_get( 'archivers' ) as $key => $archiver ) {
			$classname = 'Mailarchiver\Plugin\Feature\\' . $archiver['handler'];
			if ( class_exists( $classname ) ) {
				$archiver['uuid'] = $key;
				$instance         = $this->create_instance( $classname );
				$instance->set_archiver( $archiver );
				$instance->update( $from );
			}
		}
	}

}
