<?php
/**
 * Abstract HTTP handler for Monolog
 *
 * Handles all features of abstract HTTP handler for Monolog.
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
 * Define the Monolog abstract HTTP handler.
 *
 * Handles all features of abstract HTTP handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
abstract class AbstractBufferedHTTPHandler extends AbstractProcessingHandler {

	/**
	 * Post args.
	 *
	 * @since  2.5.0
	 * @var    array    $post_args    The args for the post request.
	 */
	protected $post_args = [];

	/**
	 * URL to post.
	 *
	 * @since  2.5.0
	 * @var    string    $endpoint    The url.
	 */
	protected $endpoint = '';

	/**
	 * Verb to use.
	 *
	 * @since  2.5.0
	 * @var    string    $verb    The verb to use.
	 */
	protected $verb = 'POST';

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
		$this->buffered  = $buffered;
		$this->post_args = [
			'headers'    => [
				'User-Agent'          => Http::user_agent(),
				'Mailarchiver-No-Log' => 'outbound',
			],
			'user-agent' => Http::user_agent(),
		];
	}

	/**
	 * Post the record to the service.
	 *
	 * @param   array $record    The record to post.
	 * @since    2.5.0
	 */
	protected function write( array $record ): void {
		if ( '' !== $this->post_args['body'] ) {
			if ( 'POST' === $this->verb ) {
				$result = wp_remote_post( esc_url_raw( $this->endpoint ), $this->post_args );
			}
			if ( 'GET' === $this->verb ) {
				$result = wp_remote_get( esc_url_raw( $this->endpoint ), $this->post_args );
			}
		}
		$code    = wp_remote_retrieve_response_code( $result );
		$message = wp_remote_retrieve_response_message( $result );
		if ( '' === $message ) {
			$message = 'Unknow error';
		}
		if ( $this->error_control ) {
			$message = 'Archiving mail to ' . $this->endpoint . ' => ' . $message;
			if ( in_array( (int) $code, Http::$http_effective_pass_codes, true ) ) {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->debug( $message, [ 'code' => $code ] );
			} elseif ( in_array( (int) $code, Http::$http_success_codes, true ) ) {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->info( $message, [ 'code' => $code ] );
			} elseif ( '' === $code ) {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( $message, [ 'code' => 999 ] );
			} else {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( $message, [ 'code' => $code ] );
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle( array $record ): bool {
		if ( $record['level'] < $this->level ) {
			return false;
		}
		$this->buffer[] = $this->processRecord( $record );
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
