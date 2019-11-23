<?php
/**
 * Listeners handling
 *
 * Handles all listeners operations.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Listener;

use Mailarchiver\Plugin\Feature\Log;
use Mailarchiver\System\Option;

/**
 * Define the listeners handling functionality.
 *
 * Handles all listeners operations.
 *
 * @package Listeners
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class ListenerFactory {

	/**
	 * An instance of DArchiver to log internal events.
	 *
	 * @since  1.0.0
	 * @var    DArchiver    $log    An instance of DArchiver to log internal events.
	 */
	private $log = null;

	/**
	 * Excluded files from listeners auto loading.
	 *
	 * @since  1.0.0
	 * @var    array    $excluded_files    The list of excluded files.
	 */
	private $excluded_files = [
		'..',
		'.',
		'index.php',
		'class-abstractlistener.php',
		'class-listenerfactory.php',
	];

	/**
	 * Excluded files from early listeners auto loading.
	 *
	 * @since  1.6.0
	 * @var    array    $late_init    The list of excluded files.
	 */
	private $late_init = [];

	/**
	 * Infos on all loadable listeners.
	 *
	 * @since  1.0.0
	 * @var    array    $excluded_files    The list of all loadable listeners.
	 */
	public static $infos = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->log = Log::bootstrap( 'plugin', MAILARCHIVER_PRODUCT_SHORTNAME, MAILARCHIVER_VERSION );
	}

	/**
	 * Launch the listeners.
	 *
	 * @since    1.0.0
	 */
	public function launch() {
		self::$infos = [];
		foreach (
			array_diff( scandir( MAILARCHIVER_LISTENERS_DIR ), $this->excluded_files, $this->late_init ) as $item ) {
			if ( ! is_dir( MAILARCHIVER_LISTENERS_DIR . $item ) ) {
				$classname = str_replace( [ 'class-', '.php' ], '', $item );
				$classname = str_replace( 'listener', 'Listener', strtolower( $classname ) );
				$classname = ucfirst( $classname );
				$instance  = $this->create_listener_instance( $classname );
				if ( $instance ) {
					self::$infos[] = $instance->get_info();
				} else {
					$this->log->error( sprintf( 'Unable to load "%s".', $classname ) );
				}
			}
		}
	}

	/**
	 * Launch the listeners which need to be launched at the end of plugin load sequence.
	 *
	 * @since    1.6.0
	 */
	public function launch_late_init() {
		foreach (
			array_intersect( scandir( MAILARCHIVER_LISTENERS_DIR ), $this->late_init ) as $item ) {
			if ( ! is_dir( MAILARCHIVER_LISTENERS_DIR . $item ) ) {
				$classname = str_replace( [ 'class-', '.php' ], '', $item );
				$classname = str_replace( 'listener', 'Listener', strtolower( $classname ) );
				$classname = ucfirst( $classname );
				$instance  = $this->create_listener_instance( $classname );
				if ( $instance ) {
					self::$infos[] = $instance->get_info();
				} else {
					$this->log->error( sprintf( 'Unable to load "%s".', $classname ) );
				}
			}
		}
	}

	/**
	 * Create an instance of a listener.
	 *
	 * @param   string $class_name The class name.
	 * @return  boolean|object The instance of the class if creation was possible, null otherwise.
	 * @since    1.0.0
	 */
	private function create_listener_instance( $class_name ) {
		$class_name = 'Mailarchiver\Listener\\' . $class_name;
		if ( class_exists( $class_name ) ) {
			try {
				$reflection = new \ReflectionClass( $class_name );
				return $reflection->newInstanceArgs( [ $this->log ] );
			} catch ( Exception $e ) {
				return false;
			}
		}
		return false;
	}

}
