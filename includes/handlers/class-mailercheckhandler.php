<?php
/**
 * MailerCheck handler for Monolog
 *
 * Handles all features of MailerCheck handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Handler;

use MAMonolog\Logger;

/**
 * Define the Monolog MailerCheck handler.
 *
 * Handles all features of MailerCheck handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class MailerCheckHandler extends AbstractBufferedMailHandler {

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
	 * @param   integer $box        The mailboxes.
	 * @param   integer $level      Optional. The min level to log.
	 * @param   boolean $bubble     Optional. Has the record to bubble?.
	 * @since    1.0.0
	 */
	public function __construct( $box, $level = Logger::INFO, bool $bubble = true ) {
		parent::__construct( $level, true, $bubble );
		$this->to = array_filter( array_map( 'trim', explode( PHP_EOL, $box ) ), 'strlen' );
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
