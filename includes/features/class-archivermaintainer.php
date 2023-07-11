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
use Mailarchiver\Plugin\Feature\EventTypes;

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

	/**
	 * Finalize the archiver.
	 *
	 * @since    1.0.0
	 */
	public function finalize() {
		foreach ( Option::network_get( 'archivers' ) as $key => $archiver ) {
			$classname = 'Mailarchiver\Plugin\Feature\\' . $archiver['handler'];
			if ( class_exists( $classname ) ) {
				$archiver['uuid'] = $key;
				$instance         = $this->create_instance( $classname );
				$instance->set_archiver( $archiver );
				$instance->finalize();
			}
		}
	}

	/**
	 * Get archivers debug info (for Site Health).
	 *
	 * @return array    The archivers definitions.
	 * @since    1.0.0
	 */
	public function debug_info() {
		$result = [];
		foreach ( Option::network_get( 'archivers' ) as $key => $logger ) {
			$name = $logger['name'];
			unset( $logger['name'] );
			$logger['uuid']    = '{' . $key . '}';
			$logger['running'] = $logger['running'] ? 'yes' : 'no';
			$logger['level']   = strtolower( EventTypes::$level_names[ $logger['level'] ] );
			$privacy           = [];
			foreach ( $logger['privacy'] as $i => $item ) {
				if ( $item ) {
					$privacy[] = $i;
				}
			}
			$security = [];
			foreach ( $logger['security'] as $i => $item ) {
				if ( $item ) {
					$security[] = $i;
				}
			}
			$logger['privacy']    = '[' . implode(', ', $privacy ) . ']';
			$logger['security']   = '[' . implode(', ', $security ) . ']';
			$logger['processors'] = '[' . implode(', ', $logger['processors'] ) . ']';
			$configuration        = [];
			foreach ( $logger['configuration'] as $i => $item ) {
				if ( in_array( $i, [ 'webhook', 'token', 'user', 'users', 'filename', 'pass', 'cloudid', 'key' ], true ) ) {
					$configuration[] = $i . ':xxx';
				} else {
					$configuration[] = $i . ':' . ( is_bool( $item ) ? ( $item ? 'false' : 'true' ) : $item );
				}
			}
			$logger['configuration'] = '[' . implode(', ', $configuration ) . ']';
			$result[ $key ]          = [ 'label' => $name, 'value' => $logger ];
		}
		return $result;
	}

}
