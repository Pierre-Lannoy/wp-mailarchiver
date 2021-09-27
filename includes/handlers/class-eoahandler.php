<?php
/**
 * Email On Acid handler for Monolog
 *
 * Handles all features of Email On Acid handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Handler;

use MAMonolog\Logger;

/**
 * Define the Monolog Email On Acid handler.
 *
 * Handles all features of Email On Acid handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class EOAHandler extends AbstractBufferedMailHandler {

	/**
	 * The new recipients().
	 *
	 * @since  2.5.0
	 * @var    array    $to    The new recipient(s).
	 */
	private $to = [];

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param   string  $host       The host part of the mail.
	 * @param   integer $box        The local-part of the mail.
	 * @param   integer $level      Optional. The min level to log.
	 * @param   boolean $bubble     Optional. Has the record to bubble?.
	 * @since    1.0.0
	 */
	public function __construct( $host, $box, $level = Logger::INFO, bool $bubble = true ) {
		parent::__construct( $level, true, $bubble );
		$this->to[] = $box . $host;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepare( array $record ): array {
		$message                = [];
		$message['to']          = $this->to;
		$message['subject']     = array_key_exists( 'subject', $record['context'] ) ? $record['context']['subject'] : '';
		$message['message']     = array_key_exists( 'body', $record['context'] ) && array_key_exists( 'raw', $record['context']['body'] ) ? $record['context']['body']['raw'] : '';
		$message['headers']     = array_key_exists( 'headers', $record['context'] ) ? $record['context']['headers'] : '';
		$message['attachments'] = array_key_exists( 'attachments', $record['context'] ) ? $record['context']['attachments'] : '';
		return $message;
	}

}
