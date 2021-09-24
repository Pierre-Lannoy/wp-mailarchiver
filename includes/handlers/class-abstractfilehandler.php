<?php
/**
 * Base file handler for Monolog
 *
 * Handles all features of base file handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Handler;

use MAMonolog\Logger;
use MAMonolog\Handler\AbstractProcessingHandler;
use MAMonolog\Formatter\FormatterInterface;
use Mailarchiver\Formatter\EmlFileFormatter;
use Mailarchiver\Formatter\JsonFileFormatter;

/**
 * Define the Monolog base file handler.
 *
 * Handles all features of base file handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
abstract class AbstractFileHandler extends AbstractProcessingHandler {

	/**
	 * Used format.
	 *
	 * @since  2.5.0
	 * @var    integer    $format    The format to use.
	 */
	protected $format = 100;

	/**
	 * The buffer.
	 *
	 * @since  2.5.0
	 * @var    array    $buffer    The buffer.
	 */
	private $buffer = [];

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
	 * @param   integer $format     Optional. The format in which saving.
	 * @param   integer $level      Optional. The min level to log.
	 * @param   boolean $bubble     Optional. Has the record to bubble?.
	 * @since    1.0.0
	 */
	public function __construct( $format = 100, $level = Logger::INFO, bool $bubble = true ) {
		parent::__construct( $level, $bubble );
		$this->format = $format;
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getDefaultFormatter(): FormatterInterface {
		switch ( $this->format ) {
			case 200:
				return new JsonFileFormatter();
			default:
				return new EmlFileFormatter();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle( array $record ): bool {
		$this->buffer[] = $this->getFormatter()->format( $this->processRecord( $record ) );
		if ( ! $this->initialized ) {
			add_action( 'shutdown', [ $this, 'close' ], MAILARCHIVER_MAX_SHUTDOWN_PRIORITY + 500, 0 );
			$this->initialized = true;
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
		foreach ( $this->buffer as $record ) {
			$this->write_file( $record );
		}
		$this->buffer = [];
	}

	/**
	 * Writes the record in a file.
	 *
	 * @param   array $record    The record to write.
	 * @since    2.5.0
	 */
	abstract protected function write_file(array $record): void;

	/**
	 * {@inheritdoc}
	 */
	protected function write(array $record): void {

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
