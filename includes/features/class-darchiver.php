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
use MAMonolog\Logger;
use Mailarchiver\System\Environment;
use Mailarchiver\System\Option;
use Mailarchiver\System\Timezone;
use Mailarchiver\Plugin\Feature\ArchiverFactory;
use Mailarchiver\Plugin\Feature\ClassTypes;
use Mailarchiver\Plugin\Feature\ChannelTypes;
use Mailarchiver\Plugin\Feature\HandlerDiagnosis;
use Mailarchiver\System\UUID;

/**
 * Main MailArchiver archiver class.
 *
 * This class defines all code necessary to archive mails with MailArchiver.
 *
 * @package Features
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */
class DArchiver {

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
		if ( ! defined( 'DECALOG_TRACEID' ) ) {
			define( 'DECALOG_TRACEID', UUID::generate_unique_id( 32 ) );
		}
		if ( in_array( $class, ClassTypes::$classes, true ) ) {
			$this->class = $class;
		}
		if ( $name && is_string( $name ) ) {
			$this->name = $name;
		}
		if ( $version && is_string( $version ) ) {
			$this->version = $version;
		}
		$this->psr3    = $psr3;
		$this->allowed = isset( $test ) || 0 === (int) Option::network_get( 'mode' ) || 2 === (int) Option::network_get( 'mode' );
		$this->init( $test );
		\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->debug( 'A new instance of MailArchiver archiver is initialized and operational.' );
	}

	/**
	 * Init the archiver.
	 *
	 * @param   string $test Optional. The handler to init if specified.
	 * @since 1.0.0
	 */
	private function init( $test = null ) {
		if ( $this->allowed ) {
			$this->in_test  = isset( $test );
			$factory        = new ArchiverFactory();
			$this->archiver = new Logger( $this->current_channel_tag(), [], [], Timezone::network_get() );
			$handlers       = new HandlerTypes();
			$diagnosis      = new HandlerDiagnosis();
			$unloadable     = [];
			foreach ( Option::network_get( 'archivers' ) as $key => $archiver ) {
				if ( $this->in_test && $key !== $test ) {
					continue;
				}
				$handler_def      = $handlers->get( $archiver['handler'] );
				$archiver['uuid'] = $key;
				if ( $diagnosis->check( $handler_def['id'] ) ) {
					$handler = $factory->create_archiver( $archiver );
					if ( $handler ) {
						$this->archiver->pushHandler( $handler );
					}
				} else {
					$unloadable[] = sprintf( 'Unable to load a %s archiver. %s', $handler_def['name'], $diagnosis->error_string( $handler_def['id'] ) );
				}
			}
			if ( count( $unloadable ) > 0 ) {
				foreach ( $unloadable as $item ) {
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( $item, [ 'code' => 666 ] );
				}
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
	 * Get the mail context.
	 *
	 * @param array $mail      The mail.
	 * @return  array The context.
	 * @since 1.0.0
	 */
	private function context( $mail ) {
		$context = [
			'class'     => (string) $this->class,
			'component' => (string) $this->name,
			'version'   => (string) $this->version,
			'traceID'   => (string) DECALOG_TRACEID,
		];
		if ( is_array( $mail ) ) {
			foreach ( [ 'to', 'from', 'subject' ] as $field ) {
				if ( array_key_exists( $field, $mail ) ) {
					$context[ $field ] = $mail[ $field ];
				} else {
					$context[ $field ] = '-';
				}
			}
			$context['body'] = '';
			if ( array_key_exists( 'body', $mail ) ) {
				$context['body'] = $mail['body'];
			}
			$context['headers'] = [];
			if ( array_key_exists( 'headers', $mail ) ) {
				if ( is_array( $mail['headers'] ) ) {
					$context['headers'] = $mail['headers'];
				} elseif ( is_string( $mail['headers'] ) ) {
					$headers = explode( "\n", str_replace( "\r\n", "\n", $mail['headers'] ) );
					foreach ( $headers as $header ) {
						$header = '"' . $header . '"';
						if ( '"' === $header[0] && '"' === $header[ strlen( $header ) - 1 ] ) {
							$header = substr( $header, 1, strlen( $header ) - 2 );
						}
						$context['headers'][] = $header;
					}
				}
			}
			$context['attachments'] = [];
			if ( array_key_exists( 'attachments', $mail ) ) {
				if ( is_array( $mail['attachments'] ) ) {
					$context['attachments'] = $mail['attachments'];
				}
			}
		}
		return $context;
	}

	/**
	 * Adds a mail archive at the INFO level.
	 *
	 * @param array  $mail      The mail.
	 * @param string $message   Optional. The error message.
	 * @return  boolean     True if mail was recorded, false otherwise.
	 * @since 1.0.0
	 */
	public function success( $mail, $message = '-' ) {
		if ( ! $this->allowed ) {
			return false;
		}
		try {
			$channel = $this->current_channel_tag();
			if ( $this->archiver->getName() !== $channel ) {
				$this->archiver = $this->archiver->withName( $channel );
			}
			$result = true;
			// phpcs:ignore
			set_error_handler( function () use (&$result) {$result = false;} );
			$this->archiver->info( filter_var( $message, FILTER_SANITIZE_FULL_SPECIAL_CHARS ), $this->context( $mail ) );
			// phpcs:ignore
			restore_error_handler();
		} catch ( \Throwable $t ) {
			$result = false;
			\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( $t->getMessage(), $t->getCode() );
		} finally {
			if ( ! $result ) {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( 'Unable to archive an email.' );
			}
			return $result;
		}
	}

	/**
	 * Adds a mail archive at the ERROR level.
	 *
	 * @param array  $mail      The mail.
	 * @param string $message   Optional. The error message.
	 * @return  boolean     True if mail was recorded, false otherwise.
	 * @since 1.0.0
	 */
	public function error( $mail, $message = '-' ) {
		if ( ! $this->allowed ) {
			return false;
		}
		try {
			$channel = $this->current_channel_tag();
			if ( $this->archiver->getName() !== $channel ) {
				$this->archiver = $this->archiver->withName( $channel );
			}
			$result = true;
			// phpcs:ignore
			set_error_handler( function () use (&$result) {$result = false;} );
			$this->archiver->error( filter_var( $message, FILTER_SANITIZE_FULL_SPECIAL_CHARS ), $this->context( $mail ) );
			// phpcs:ignore
			restore_error_handler();
		} catch ( \Throwable $t ) {
			$result = false;
			\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( $t->getMessage(), $t->getCode() );
		} finally {
			if ( ! $result ) {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( 'Unable to archive an email.' );
			}
			return $result;
		}
	}

}
