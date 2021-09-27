<?php
/**
 * Mail-forwarder handler for Monolog
 *
 * Handles all features of the mail-forwarder handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

namespace Mailarchiver\Handler;

use MAMonolog\Logger;

/**
 * Define the Monolog mail-forwarder handler.
 *
 * Handles all features of the mail-forwarder handler for Monolog.
 *
 * @package Handlers
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */
class MailForwardHandler extends AbstractBufferedMailHandler {

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
		$this->to = $box;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function prepare( array $record ): array {
		$to          = array_key_exists( 'to', $record['context'] ) ? $record['context']['to'] : '';
		$subject     = array_key_exists( 'subject', $record['context'] ) ? $record['context']['subject'] : '';
		$body        = array_key_exists( 'body', $record['context'] ) && array_key_exists( 'raw', $record['context']['body'] ) ? $record['context']['body']['raw'] : '';
		$headers     = array_key_exists( 'headers', $record['context'] ) ? $record['context']['headers'] : [];
		$attachments = array_key_exists( 'attachments', $record['context'] ) ? $record['context']['attachments'] : '';
		if ( is_string( $headers ) ) {
			$headers = [ $headers ];
		}
		$phpmailer = mailarchiver_wp_mail( $to, $subject, $body, $headers, $attachments );
		$phpmailer->preSend();
		$source = $phpmailer->getSentMIMEMessage();
		foreach ( [ 'From', 'Date', 'Subject', 'To' ] as $field ) {
			if ( preg_match( '/^' . $field . ': (.*)$/imu', $source, $matches ) ) {
				if ( 1 < count( $matches ) ) {
					$headers[] = 'Resent-' . $field . ': ' . $matches[1];
				}
			}
		}
		$headers[]              = 'Autoforwarded: true';
		$message                = [];
		$message['to']          = $this->to;
		$message['subject']     = ( isset( $record['level'] ) && 200 === (int) $record['level'] ? 'OK' : 'KO' ) . ': ' . $subject;
		$message['message']     = $body;
		$message['headers']     = $headers;
		$message['attachments'] = $attachments;
		return $message;
	}

}
