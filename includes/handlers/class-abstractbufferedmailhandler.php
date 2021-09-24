<?php
/**
 * Abstract mail handler for Monolog
 *
 * Handles all features of abstract mail handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Handler;

use Mailarchiver\System\Http;
use MAMonolog\Logger;
use MAMonolog\Handler\AbstractProcessingHandler;

/**
 * Define the Monolog abstract mail handler.
 *
 * Handles all features of abstract mail handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
abstract class AbstractBufferedMailHandler extends AbstractProcessingHandler {

	/**
	 * The buffer.
	 *
	 * @since  2.5.0
	 * @var    array    $buffer    The buffer.
	 */
	private $buffer = [];

	/**
	 * Is it buffered or direct?.
	 *
	 * @since  2.5.0
	 * @var    boolean    $buffered    Is it buffered or direct?.
	 */
	private $buffered;

	/**
	 * Error control.
	 *
	 * @since  2.5.0
	 * @var    boolean    $error_control    Error control.
	 */
	protected $error_control = true;

	/**
	 * Is the handler initialized?.
	 *
	 * @since  2.5.0
	 * @var    boolean    $initialized    Is the handler initialized?.
	 */
	private $initialized = false;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   integer $level      Optional. The min level to log.
	 * @param   boolean $buffered   Optional. Has the record to be buffered?.
	 * @param   boolean $bubble     Optional. Has the record to bubble?.
	 * @since    1.0.0
	 */
	public function __construct( $level = Logger::INFO, bool $buffered = true, bool $bubble = true ) {
		parent::__construct( $level, $bubble );
		$this->buffered = $buffered;
	}

	/**
	 * Mail the record.
	 *
	 * @param   array $record    The record to post.
	 * @since    2.5.0
	 */
	protected function write( array $record ): void {
		if ( ! $record['headers'] ) {
			$record['headers'] = [];
		}
		$record['headers'][] = 'X-Simulator: ' . MAILARCHIVER_PRODUCT_SHORTNAME . ' ' . MAILARCHIVER_VERSION;
		if ( wp_mail( $record['to'], $record['subject'], $record['message'], $record['headers'], $record['attachments'] ) && $this->error_control ) {
			\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->debug( sprintf( 'Mail copy successfully sent to %s.', is_array( $record['to'] ) ? implode( ', ', $record['to'] ) : $record['to'] ) );
		} elseif ( $this->error_control ) {
			\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( sprintf( 'Unable to send a mail copy to %s.', is_array( $record['to'] ) ? implode( ', ', $record['to'] ) : $record['to'] ), [ 'code' => 500 ] );
		}
	}

	/**
	 * Prepare the record.
	 *
	 * @param   array $record    The record to prepare.
	 * @return  array   The prepared record.
	 * @since    2.5.0
	 */
	abstract protected function prepare( array $record ): array;

	/**
	 * {@inheritdoc}
	 */
	public function handle( array $record ): bool {
		if ( $record['level'] < $this->level ) {
			return false;
		}
		$this->buffer[] = $this->prepare( $record );
		if ( $this->buffered ) {
			if ( ! $this->initialized ) {
				add_action( 'shutdown', [ $this, 'close' ], MAILARCHIVER_MAX_SHUTDOWN_PRIORITY + 500, 0 );
				$this->initialized = true;
			}
		} else {
			$this->flush();
		}
		return false === $this->bubble;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handleBatch( array $records ): void {
		foreach ( $records as $record ) {
			$this->write( $record );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function flush(): void {
		if ( 0 === count( $this->buffer ) ) {
			return;
		}
		$this->handleBatch( $this->buffer );
		$this->buffer = [];
	}

	/**
	 * {@inheritdoc}
	 */
	public function __destruct() {
		// suppress the parent behavior since we already have register_shutdown_function()
		// to call close(), and the reference contained there will prevent this from being
		// GC'd until the end of the request
	}

	/**
	 * {@inheritdoc}
	 */
	public function close(): void {
		$this->flush();
		parent::close();
	}

}
