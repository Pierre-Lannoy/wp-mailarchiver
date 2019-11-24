<?php
/**
 * MailArchiver archiver definition.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

namespace Mailarchiver\Plugin\Feature;

use Mailarchiver\Plugin\Feature\HandlerTypes;
use Monolog\Logger;
use Mailarchiver\System\Environment;
use Mailarchiver\System\Option;
use Mailarchiver\System\Timezone;
use Mailarchiver\Plugin\Feature\ArchiverFactory;
use Mailarchiver\Plugin\Feature\ClassTypes;
use Mailarchiver\Plugin\Feature\ChannelTypes;
use Mailarchiver\Plugin\Feature\HandlerDiagnosis;
use Mailarchiver\System\Logger as InternalLogger;

/**
 * Main MailArchiver archiver class.
 *
 * This class defines all code necessary to log events with MailArchiver.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class DArchiver {

	/**
	 * The banned classes.
	 *
	 * @since  1.0.0
	 * @var    array    $banned    Maintains the list of banned classes.
	 */
	private static $banned = [];

	/**
	 * The class of the component.
	 *
	 * @since  1.0.0
	 * @var    string    $class    Maintains the class of the component.
	 */
	protected $class = 'unknwon';

	/**
	 * The name of the component.
	 *
	 * @since  1.0.0
	 * @var    string    $class    Maintains the name of the component.
	 */
	protected $name = 'unknown';

	/**
	 * The version of the component.
	 *
	 * @since  1.0.0
	 * @var    string    $version    Maintains the version of the component.
	 */
	protected $version = '-';

	/**
	 * The monolog archiver.
	 *
	 * @since  1.0.0
	 * @var    object    $archiver    Maintains the archiver.
	 */
	protected $archiver = null;

	/**
	 * Is the archiver in test.
	 *
	 * @since  1.2.1
	 * @var    boolean    $in_test    Maintains the test status of the archiver.
	 */
	protected $in_test = false;

	/**
	 * Is the autolistening mode on.
	 *
	 * @since  1.3.0
	 * @var    boolean    $autolisten    Maintains the autolistenning status of the archiver.
	 */
	private $autolisten = true;

	/**
	 * Is this listener a PSR-3 archiver.
	 *
	 * @since  1.3.0
	 * @var    boolean    $autolisten    Maintains the psr3 status of the archiver.
	 */
	private $psr3 = false;

	/**
	 * Is archiver allowed to run.
	 *
	 * @since  1.3.0
	 * @var    boolean    $allowed    Maintains the allowed status of the archiver.
	 */
	private $allowed = true;

	/**
	 * The bannissable extra classes.
	 *
	 * @since  1.0.0
	 * @var    array    $bannissable    Maintains the bannissable extra classes.
	 */
	private static $bannissable = [ 'ssl://api.pushover.net' => 'pshhandler' ];

	/**
	 * Temporarily ban a class.
	 *
	 * @param  string $classname The class name to ban.
	 * @param  string $message Optional. The message of the initial error.
	 * @since 1.0.0
	 */
	public static function ban( $classname, $message = '' ) {
		if ( '' !== $message ) {
			foreach ( self::$bannissable as $key => $val ) {
				if ( false !== strpos( $message, $key ) ) {
					if ( ! in_array( $val, self::$banned, true ) ) {
						self::$banned[] = $val;
					}
				}
			}
		}
		while ( false !== strpos( $classname, '/' ) ) {
			$classname = substr( $classname, strpos( $classname, '/' ) + 1 );
		}
		$classname = str_replace( '.php', '', strtolower( $classname ) );
		if ( ! in_array( $classname, self::$banned, true ) ) {
			self::$banned[] = $classname;
		}
	}

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string  $class The class identifier, must be in self::$classes.
	 * @param   string  $name Optional. The name of the component.
	 * @param   string  $version Optional. The version of the component.
	 * @param   string  $test Optional. The handler to create if specified.
	 * @param   boolean $psr3 Optional. True if this archiver is a PSR-3 archiver.
	 * @since   1.0.0
	 */
	public function __construct( $class, $name = null, $version = null, $test = null, $psr3 = false ) {
		if ( in_array( $class, ClassTypes::$classes, true ) ) {
			$this->class = $class;
		}
		if ( $name && is_string( $name ) ) {
			$this->name = $name;
		}
		if ( $version && is_string( $version ) ) {
			$this->version = $version;
		}
		$this->psr3 = $psr3;
		$this->init( $test );
		InternalLogger::debug( 'A new instance of MailArchiver archiver is initialized and operational.' );
	}

	/**
	 * Init the archiver.
	 *
	 * @param   string $test Optional. The handler to init if specified.
	 * @since 1.0.0
	 */
	private function init( $test = null ) {
		if ( $this->psr3 ) {
			if ( ! Option::network_get( 'autolisteners' ) ) {
				$this->allowed = in_array( 'psr3', Option::network_get( 'listeners' ), true );
			}
		}
		$this->in_test  = isset( $test );
		$factory        = new ArchiverFactory();
		$this->archiver = new Logger( $this->current_channel_tag(), [], [], Timezone::network_get() );
		$handlers       = new HandlerTypes();
		$diagnosis      = new HandlerDiagnosis();
		$banned         = [];
		$unloadable     = [];
		foreach ( Option::network_get( 'archivers' ) as $key => $archiver ) {
			if ( $this->in_test && $key !== $test ) {
				continue;
			}
			$handler_def      = $handlers->get( $archiver['handler'] );
			$archiver['uuid'] = $key;
			if ( $this->in_test || ( ! in_array( strtolower( $handler_def['ancestor'] ), self::$banned, true ) && ! in_array( strtolower( $handler_def['id'] ), self::$banned, true ) ) ) {
				if ( $diagnosis->check( $handler_def['id'] ) ) {
					$handler = $factory->create_archiver( $archiver );
					if ( $handler ) {
						$this->archiver->pushHandler( $handler );
					}
				} else {
					$unloadable[] = sprintf( 'Unable to load a %s archiver. %s', $handler_def['name'], $diagnosis->error_string( $handler_def['id'] ) );
				}
			} else {
				$banned[] = $handler_def['name'];
			}
		}
		if ( count( $banned ) > 0 ) {
			// phpcs:ignore
			InternalLogger::critical( sprintf ('Due to MailArchiver internal errors, the following archiver types have been temporarily deactivated: %s.', implode(', ', $banned ) ), 666 );
		}
		if ( count( $unloadable ) > 0 ) {
			foreach ( $unloadable as $item ) {
				InternalLogger::error( $item, 666 );
			}
		}
	}

	/**
	 * Check the integrity of the archiver.
	 *
	 * @since 1.0.0
	 */
	private function integrity_check() {
		if ( count( self::$banned ) > 0 && ! $this->in_test ) {
			$handlers = new HandlerTypes();
			$banned   = [];
			foreach ( $this->archiver->getHandlers() as $handler ) {
				$classname = get_class( $handler );
				while ( false !== strpos( $classname, '\\' ) ) {
					$classname = substr( $classname, strpos( $classname, '\\' ) + 1 );
				}
				$handler_def = $handlers->get( $classname );
				$ancestor    = $handler_def['ancestor'];
				if ( in_array( strtolower( $classname ), self::$banned, true ) || in_array( strtolower( $ancestor ), self::$banned, true ) ) {
					$this->archiver->popHandler( $handler );
					$banned[] = $handler_def['name'];
				}
			}
			if ( count( $banned ) > 0 ) {
				// phpcs:ignore
				InternalLogger::critical( sprintf ('Due to MailArchiver internal errors, the following archiver types have been temporarily deactivated: %s.', implode(', ', $banned ) ), 666 );
			}
		}
	}

	/**
	 * Get the current channel tag.
	 *
	 * @return  string The current channel tag.
	 * @since 1.0.0
	 */
	private function current_channel_tag() {
		return $this->channel_tag( Environment::exec_mode() );
	}

	/**
	 * Get the channel tag.
	 *
	 * @param   integer $id Optional. The channel id (execution mode).
	 * @return  string The channel tag.
	 * @since 1.0.0
	 */
	public function channel_tag( $id = 0 ) {
		if ( $id >= count( ChannelTypes::$channels ) ) {
			$id = 0;
		}
		return ChannelTypes::$channels[ $id ];
	}

	/**
	 * Adds a mail archive at the INFO level.
	 *
	 * @param array  $mail      The mail.
	 * @param string $message   Optional. The error message.
	 * @since 1.0.0
	 */
	public function success( $mail, $message = '-') {
		if ( ! $this->allowed ) {
			return;
		}
		try {
			$context = [
				'class'     => (string) $this->class,
				'component' => (string) $this->name,
				'version'   => (string) $this->version,
			];
			if ( is_array( $mail ) ) {
				foreach ( [ 'to', 'from', 'subject', 'body', 'headers', 'attachments' ] as $field ) {
					if ( array_key_exists( $field, $mail ) ) {
						$context[ $field ] = $mail[ $field ];
					}
				}
			}
			$channel = $this->current_channel_tag();
			if ( $this->archiver->getName() !== $channel ) {
				$this->archiver = $this->archiver->withName( $channel );
			}
			$this->archiver->info( filter_var( $message, FILTER_SANITIZE_STRING ), $context );
			$result = true;
		} catch ( \Throwable $t ) {
			$this->integrity_check();
			$result = false;
		} finally {
			return $result;
		}
	}

	/**
	 * Adds a mail archive at the ERROR level.
	 *
	 * @param array  $mail      The mail.
	 * @param string $message   Optional. The error message.
	 * @since 1.0.0
	 */
	public function error( $mail, $message = '-') {
		if ( ! $this->allowed ) {
			return;
		}
		try {
			$context = [
				'class'     => (string) $this->class,
				'component' => (string) $this->name,
				'version'   => (string) $this->version,
			];
			if ( is_array( $mail ) ) {
				foreach ( [ 'to', 'from', 'subject', 'body', 'headers', 'attachments' ] as $field ) {
					if ( array_key_exists( $field, $mail ) ) {
						$context[ $field ] = $mail[ $field ];
					}
				}
			}
			$channel = $this->current_channel_tag();
			if ( $this->archiver->getName() !== $channel ) {
				$this->archiver = $this->archiver->withName( $channel );
			}
			$this->archiver->error( filter_var( $message, FILTER_SANITIZE_STRING ), $context );
			$result = true;
		} catch ( \Throwable $t ) {
			$this->integrity_check();
			$result = false;
		} finally {
			return $result;
		}
	}

}
