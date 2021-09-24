<?php
/**
 * Individual file handler for Monolog
 *
 * Handles all features of individual file handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Handler;

use MAMonolog\Logger;

/**
 * Define the Monolog individual file handler.
 *
 * Handles all features of individual file handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class IndividualFileHandler extends AbstractFileHandler {

	/**
	 * The path where to write.
	 *
	 * @since  2.5.0
	 * @var    string    $path    The path.
	 */
	protected $path = '';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string  $path       The path where to write.
	 * @param   integer $format     Optional. The format in which saving.
	 * @param   integer $level      Optional. The min level to log.
	 * @param   boolean $bubble     Optional. Has the record to bubble?.
	 * @since    1.0.0
	 */
	public function __construct( $path, $format = 100, $level = Logger::INFO, bool $bubble = true ) {
		parent::__construct( $format, $level, $bubble );
		if ( isset( $path ) && '' !== $path ) {
			$this->path = $path;
			if ( '/' !== substr( $this->path, -1 ) ) {
				$this->path = $this->path . '/';
			}
		}
	}

	/**
	 * Write the record in the table.
	 *
	 * @param   array $record    The record to write.
	 * @since    1.0.0
	 */
	protected function write_file( array $record ): void {
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}
		if ( is_array( $record ) ) {
			if ( ! file_exists( $this->path ) ) {
				if ( wp_mkdir_p( $this->path ) ) {
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->info( sprintf( 'Directory "%s" successfully created.', $this->path ) );
				} else {
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( sprintf( 'Unable to create "%s" directory.', $this->path ) );
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->critical( 'Unable to archive sent emails.' );
					return;
				}
			}
			if ( ! is_dir( $this->path ) ) {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( sprintf( '"%s" is not a directory.', $this->path ) );
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->critical( 'Unable to archive sent emails.' );
				return;
			}
			if ( ! is_writable( $this->path ) ) {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( sprintf( '"%s" is not writable.', $this->path ) );
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->critical( 'Unable to archive sent emails.' );
				return;
			}
			if ( array_key_exists( 'filename', $record ) && array_key_exists( 'content', $record ) ) {
				$file = $this->path . $record['filename'];
				if ( ! file_exists( $file ) ) {
					try {
						if ( ! $wp_filesystem->put_contents( $file, $record['content'], FS_CHMOD_FILE ) ) {
							\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( sprintf( 'Error while writing in the file "%s".', $file ) );
							\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( 'Unable to archive a sent email.' );
						}
						if ( $wp_filesystem->errors->has_errors() ) {
							foreach ( $wp_filesystem->errors->get_error_messages() as $message ) {
								\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( $message );
							}
						}
					} catch ( \Throwable $e ) {
						\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( $e->getMessage(), [ 'code' => $e->getCode() ] );
					}
				} else {
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->warning( sprintf( 'The file "%s" already exists.', $file ) );
					\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( 'Unable to archive a sent email.' );
				}
			} else {
				\DecaLog\Engine::eventsLogger( MAILARCHIVER_SLUG )->error( 'Unable to archive a malformed email.' );
			}
		}
	}

}
